<div>
    {{-- Auto-save status indicator --}}
    @if ($saveStatus === 'saving')
        <div class="draft-status draft-status--saving">
            <span class="draft-spinner"></span> Saving draft…
        </div>
    @elseif ($saveStatus === 'saved')
        <div class="draft-status draft-status--saved">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            Draft saved
        </div>
    @endif

    {{-- Draft form --}}
    <form wire:submit.prevent="autoSave" class="draft-form" autocomplete="on">

        <div class="draft-field">
            <label>Full Name</label>
            <input type="text"
                   wire:model.live.debounce.800ms="name"
                   placeholder="Your name"
                   autocomplete="name">
        </div>

        <div class="draft-field">
            <label>Phone</label>
            <input type="tel"
                   wire:model.live.debounce.800ms="phone"
                   placeholder="01XXXXXXXXX"
                   autocomplete="tel">
        </div>

        <div class="draft-field">
            <label>Email <span class="optional">(optional)</span></label>
            <input type="email"
                   wire:model.live.debounce.800ms="email"
                   placeholder="you@example.com"
                   autocomplete="email">
        </div>

        <div class="draft-field">
            <label>Address</label>
            <textarea wire:model.live.debounce.800ms="address"
                      rows="3"
                      placeholder="Delivery address"></textarea>
        </div>

        <div class="draft-field">
            <label>Delivery Area</label>
            <select wire:model.live="delivery_area_id">
                <option value="">— Select area —</option>
                @foreach ($deliveryAreas as $area)
                    <option value="{{ $area->id }}">
                        {{ $area->name }}
                        @if ($area->charge > 0)
                            (৳{{ number_format($area->charge, 0) }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="draft-field">
            <label>Payment Method</label>
            <select wire:model.live="payment_method">
                <option value="cod">Cash on Delivery</option>
                <option value="bKash">bKash</option>
            </select>
        </div>

        <div class="draft-field">
            <label>Notes <span class="optional">(optional)</span></label>
            <textarea wire:model.live.debounce.800ms="notes"
                      rows="2"
                      placeholder="Any special instructions"></textarea>
        </div>

        {{-- Order summary --}}
        @if ($subtotal > 0 || $draftId)
            <div class="draft-summary">
                <div class="draft-summary-row">
                    <span>Subtotal</span>
                    <span>৳{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="draft-summary-row">
                    <span>Delivery</span>
                    <span>৳{{ number_format($deliveryCharge, 2) }}</span>
                </div>
                <div class="draft-summary-row draft-summary-total">
                    <span>Total</span>
                    <span>৳{{ number_format($total, 2) }}</span>
                </div>
            </div>
        @endif

        <div class="draft-actions">
            <button type="submit" class="btn btn-primary">
                Save Draft
            </button>
            @if ($draftId)
                <button type="button" wire:click="clearDraft"
                        wire:confirm="Clear this draft?"
                        class="btn btn-outline-secondary">
                    Clear Draft
                </button>
            @endif
        </div>

    </form>
</div>

<style>
.draft-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 20px;
    margin-bottom: 12px;
}
.draft-status--saving {
    background: #fff8e1;
    color: #856404;
    border: 1px solid #ffc107;
}
.draft-status--saved {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}
.draft-spinner {
    width: 10px;
    height: 10px;
    border: 2px solid #ffc107;
    border-top-color: transparent;
    border-radius: 50%;
    animation: draft-spin .6s linear infinite;
    display: inline-block;
}
@keyframes draft-spin { to { transform: rotate(360deg); } }

.draft-form { display: flex; flex-direction: column; gap: 16px; }
.draft-field { display: flex; flex-direction: column; gap: 4px; }
.draft-field label { font-size: 13px; font-weight: 600; color: #374151; }
.draft-field .optional { font-weight: 400; color: #9ca3af; }
.draft-field input,
.draft-field select,
.draft-field textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    color: #111827;
    background: #fff;
    transition: border-color .15s;
}
.draft-field input:focus,
.draft-field select:focus,
.draft-field textarea:focus {
    outline: none;
    border-color: #2377FC;
    box-shadow: 0 0 0 3px rgba(35,119,252,.1);
}

.draft-summary {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.draft-summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #6b7280;
}
.draft-summary-total {
    font-weight: 700;
    font-size: 15px;
    color: #111827;
    padding-top: 6px;
    border-top: 1px solid #e5e7eb;
    margin-top: 2px;
}

.draft-actions { display: flex; gap: 10px; }
</style>
