<p>
    <label for="protocol_id">Protokoll: </label>
    <select name="protocol_id" id="protocol_id">
        @foreach ($protocols as $protocol)
        <option label="{{ $protocol->name }}" value="{{ $protocol->id }}" @selected((int) old('protocol_id', $contact?->protocol_id) === $protocol->id)>{{ $protocol->name }}</option>
        @endforeach
    </select>
    @error('protocol_id') <span class="small">{{ $message }}</span> @enderror
</p>
<p>
    <label for="contact">Kontakt</label>
    <input type="text" name="contact" id="contact" value="{{ old('contact', $contact?->contact) }}" required>
    @error('contact') <span class="small">{{ $message }}</span> @enderror
</p>
