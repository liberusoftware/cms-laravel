<div>
    <label for="accessibility-html">Content to analyze</label>
    <textarea id="accessibility-html" wire:model.live="html" aria-describedby="accessibility-findings"></textarea>
    <ul id="accessibility-findings">
        @foreach ($findings as $finding)
            <li><strong>{{ $finding['severity'] }}</strong>: {{ $finding['message'] }}</li>
        @endforeach
    </ul>
</div>
