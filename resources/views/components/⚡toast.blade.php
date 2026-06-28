<?php

use Livewire\Component;

new class extends Component {
    //
};
?>
<div>

    @if (session()->has('success'))
        <script>
            document.addEventListener('livewire:navigated', () => {
                KTToast.show({
                    message: @js(session('success')),
                    type: 'success',
                });
            });
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            document.addEventListener('livewire:navigated', () => {
                KTToast.show({
                    message: @js(session('error')),
                    type: 'danger',
                });
            });
        </script>
    @endif

</div>
