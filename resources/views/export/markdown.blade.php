<x-main-layout title="Markdown-Export" :no-breadcrumbs="true">
    @if (session('export_markdown_status'))
    <p class="notice notice_success">{{ session('export_markdown_status') }}</p>
    @endif

    <form action="{{ route('export.markdown.store') }}" method="post">
        @csrf
        <p><input type="submit" value="Markdown-Export erstellen"></p>
    </form>

    @if ($exports)
    <table>
        <thead>
            <tr>
                <th>Datei</th>
                <th>Erstellt</th>
                <th>Größe</th>
                <th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($exports as $export)
            <tr>
                <td>{{ $export['filename'] }}</td>
                <td><x-datetime :time="$export['createdAt']" /></td>
                <td>{{ $export['size'] }}</td>
                <td>
                    <a href="{{ route('export.markdown.download', ['filename' => $export['filename']]) }}">herunterladen</a>
                    <form action="{{ route('export.markdown.destroy', ['filename' => $export['filename']]) }}" method="post" style="display: inline">
                        @csrf
                        @method('delete')
                        <input type="submit" value="löschen">
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Keine fertigen Markdown-Exporte gefunden.</p>
    @endif
</x-main-layout>
