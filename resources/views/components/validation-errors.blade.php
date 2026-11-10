@if ($errors->any())
<div class="notice notice_error" id="validation-errors" role="alert" aria-labelledby="validation-errors-heading">
    <strong id="validation-errors-heading">Bitte überprüfe deine Eingaben:</strong>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
