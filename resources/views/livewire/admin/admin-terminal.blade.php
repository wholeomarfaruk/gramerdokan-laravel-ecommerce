<div>
   <div class="card">
    <div class="card-header">
        <strong>Admin Terminal</strong>
    </div>

    <div class="card-body">
        @if($customMode)
    <input type="text"
           wire:model="customCommand"
           class="form-control mb-3"
           placeholder="Enter custom command">
@else
    <select wire:model="command" class="form-control mb-3">
        <option value="">Select command</option>
        @foreach($allowed as $item)
            <option value="{{ $item }}">{{ $item }}</option>
        @endforeach
    </select>
@endif


        <button wire:click="run" class="btn btn-primary">
            Run Command
        </button>

        <pre class="bg-dark text-light p-3 mt-3" style="min-height: 250px; white-space: pre-wrap;">{{ $output }}</pre>
    </div>
</div>
</div>
