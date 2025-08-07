<script>
    window.livewire && window.livewire.on('setBranchRouteField', function(field, value) {
        if (field && value) {
            window.dispatchEvent(new CustomEvent('filament-form-set', {
                detail: {
                    field,
                    value
                }
            }));
        }
    });
    window.addEventListener('branch-route-select', function(e) {
        if (e.detail && e.detail.field && e.detail.value) {
            window.dispatchEvent(new CustomEvent('filament-form-set', {
                detail: {
                    field: e.detail.field,
                    value: e.detail.value
                }
            }));
        }
    });
    window.addEventListener('filament-form-set', function(e) {
        if (e.detail && e.detail.field && e.detail.value) {
            let input = document.querySelector(
                `[name="data[\${e.detail.field}]"], [wire\\:model="data.\${e.detail.field}"]`);
            if (input) {
                input.value = e.detail.value;
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        }
    });
</script>
