<?php

namespace App\Livewire;

use App\Events\NewMessageEvent;
use App\Models\Conversation;
use App\Models\Message;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatWindow extends Component
{
    use WithFileUploads;

    public Conversation $conversation;
    public $messages = [];
    public $newMessage = '';
    public $attachment;

    protected $listeners = ['echo-private:conversation.{conversation.id},.message.new' => 'handleNewMessage'];

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = $this->conversation->messages()
            ->with(['sender.user', 'sender']) // Load sender details (User or Employee)
            ->oldest()
            ->get()
            ->toArray();
            
        $this->markAsRead();
    }
    
    public function markAsRead()
    {
        // Mark Customer messages as read
        $this->conversation->messages()
            ->where('sender_type', '!=', 'App\\Models\\Employee') // Assuming current user is Employee
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function handleNewMessage($event)
    {
        // Add new message to the list
        $this->messages[] = $event; // The event payload structure matches what we expect
        
        // Scroll to bottom dispatch
        $this->dispatch('scroll-to-bottom');
        
        // Mark as read if it's from customer and we are viewing
        $this->markAsRead();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:attachment|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $employee = \App\Models\Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            Notification::make()->title('Error')->body('Employee profile not found.')->danger()->send();
            return;
        }

        $attachmentUrl = null;
        $attachmentName = null;

        if ($this->attachment) {
            $path = $this->attachment->store('chat-attachments', 'public');
            $attachmentUrl = asset('storage/' . $path);
            $attachmentName = $this->attachment->getClientOriginalName();
        }

        $message = $this->conversation->messages()->create([
            'sender_type' => get_class($employee),
            'sender_id' => $employee->id,
            'content' => $this->newMessage ?? '',
            'type' => $this->attachment ? 'file' : 'text',
            'attachment_url' => $attachmentUrl,
            'attachment_name' => $attachmentName,
            'uuid' => (string) Str::uuid(),
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        // Broadcast
        broadcast(new NewMessageEvent($message))->toOthers();

        // Add to local state
        $message->load('sender');
        $this->messages[] = $message->toArray();

        $this->newMessage = '';
        $this->attachment = null;
        
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.chat-window');
    }
}
