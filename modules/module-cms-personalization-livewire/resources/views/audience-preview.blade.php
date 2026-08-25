<div aria-live="polite">
    <label for="cms-personalization-audience">Audience key</label>
    <input id="cms-personalization-audience" wire:model.live="audience">
    <label for="cms-personalization-subject">Subject</label>
    <input id="cms-personalization-subject" wire:model.live="subject">
    <label><input type="checkbox" wire:model.live="consent"> Consent granted</label>
    <button type="button" wire:click="preview" wire:loading.attr="disabled">Preview</button>
    @if($decision)<span>Reason: {{ $decision['reason'] }}</span>@endif
</div>
