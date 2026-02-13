{{--
    Flash messages — renders session flash values and validation errors as dismissible alerts.

    Usage: <x-ui.flash-messages /> — placed at the top of <main> in app.blade.php.

    Reads:
      session('success') → success alert
      session('error')   → error alert
      session('warning') → warning alert
      session('info')    → info alert
      $errors->any()     → error alert listing all validation messages
--}}

@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 space-y-3">

        @if(session('success'))
            <x-ui.alert variant="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if(session('error'))
            <x-ui.alert variant="error" dismissible>
                {{ session('error') }}
            </x-ui.alert>
        @endif

        @if(session('warning'))
            <x-ui.alert variant="warning" dismissible>
                {{ session('warning') }}
            </x-ui.alert>
        @endif

        @if(session('info'))
            <x-ui.alert variant="info" dismissible>
                {{ session('info') }}
            </x-ui.alert>
        @endif

        @if($errors->any())
            <x-ui.alert variant="error" title="Please correct the following errors:" dismissible>
                <ul class="list-disc list-inside space-y-0.5 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

    </div>
@endif
