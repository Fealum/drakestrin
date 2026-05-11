<p>
    <label for="protocol">Protokoll: </label>
    <select name="protocol" id="protocol">
        @foreach ($protocols as $protocol)
        <option label="{{ $protocol->name }}" value="{{ $protocol->id }}" @selected((int) old('protocol', $contact?->protocol) === $protocol->id)>{{ $protocol->name }}</option>
        @endforeach
    </select>
    @error('protocol') <span class="small">{{ $message }}</span> @enderror
</p>
<p>
    <label for="contact">Kontakt</label>
    <input type="text" name="contact" id="contact" value="{{ old('contact', $contact?->contact) }}" required>
    @error('contact') <span class="small">{{ $message }}</span> @enderror
</p>
