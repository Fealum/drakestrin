@if ($errors->any())
<ul class="errors">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

@isset($parent)
<p>
    Gehört zu:
    @if ($parent instanceof \App\Models\Territory\Territory)
    {{ $parent->displayName() }}
    @else
    {{ $parent->name }}
    @endif
</p>
@endisset

@isset($parentOptions)
<p>
    <label for="parent">Gehört zu</label>
    <select name="parent" id="parent" required>
        @foreach ($parentOptions as $option)
        <option value="{{ $option['key'] }}" @selected(old('parent', $selectedParent ?? '') === $option['key'])>{{ $option['label'] }}</option>
        @endforeach
    </select>
</p>
@endisset

<p>
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name', $location->name ?? '') }}" required maxlength="255">
</p>

<p>
    <label for="description">Beschreibung</label>
    <textarea id="description" name="description">{{ old('description', $location->description ?? '') }}</textarea>
</p>

<p>
    <label for="priority">Sortierung</label>
    <input id="priority" name="priority" type="number" value="{{ old('priority', $location->priority ?? 0) }}">
</p>

<input type="submit" value="{{ $submitLabel }}">
