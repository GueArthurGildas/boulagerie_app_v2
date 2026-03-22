@extends('layouts.app')

@section('title', isset($depense) ? 'Modifier dépense' : 'Nouvelle dépense')
@section('page-title', isset($depense) ? 'Modifier la dépense' : 'Nouvelle dépense')

@section('content')

<div class="page-header">
    <div>
        <h2>{{ isset($depense) ? 'Modifier : ' . $depense->libelle : 'Nouvelle Dépense' }}</h2>
        <div class="or-line"></div>
    </div>
    <a href="{{ route('depenses.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour
    </a>
</div>

@if($modele ?? false)
    <div class="alert alert-warning" style="margin-bottom:20px;">
        <i class="ri-file-copy-line"></i>
        Dépense clonée depuis <strong>{{ $modele->libelle }}</strong> — modifiez les champs nécessaires avant d'enregistrer.
    </div>
@endif

<div style="max-width:800px;">
    <form method="POST" action="{{ isset($depense) ? route('depenses.update', $depense) : route('depenses.store') }}">
        @csrf
        @if(isset($depense)) @method('PUT') @endif

        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">Informations principales</span>
            </div>
            <div class="card-body">

                <div class="form-group">
                    <label class="form-label">Libellé *</label>
                    <input type="text" name="libelle" class="form-control"
                           value="{{ old('libelle', $depense->libelle ?? $modele->libelle ?? '') }}"
                           placeholder="Ex: Achat farine, Loyer local, Carburant livraison..."
                           required>
                    @error('libelle')
                        <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Catégorie *</label>
                        <select name="categorie_depense_id" class="form-control" required>
                            <option value="">— Choisir —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('categorie_depense_id', $depense->categorie_depense_id ?? $modele->categorie_depense_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_depense_id')
                            <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Montant (FCFA) *</label>
                        <input type="number" name="montant" class="form-control"
                               value="{{ old('montant', $depense->montant ?? $modele->montant ?? '') }}"
                               min="1" step="1" placeholder="0" required>
                        @error('montant')
                            <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date de la dépense *</label>
                        <input type="date" name="date_depense" class="form-control"
                               value="{{ old('date_depense', isset($depense) ? $depense->date_depense->format('Y-m-d') : now()->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bénéficiaire</label>
                        <input type="text" name="beneficiaire" class="form-control"
                               value="{{ old('beneficiaire', $depense->beneficiaire ?? $modele->beneficiaire ?? '') }}"
                               placeholder="Nom du fournisseur ou bénéficiaire">
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">Mode de paiement</span>
            </div>
            <div class="card-body">
                {{-- Sélection mode visuelle --}}
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;" id="modeGrid">
                    @php
                        $modes = [
                            'cash'         => ['Cash',         'ri-money-dollar-circle-line', '#27AE60'],
                            'orange_money' => ['Orange Money', 'ri-smartphone-line',          '#FF6B00'],
                            'wave'         => ['Wave',         'ri-smartphone-line',          '#1A73E8'],
                            'mtn_momo'     => ['MTN MoMo',     'ri-smartphone-line',          '#FFCC00'],
                            'banque'       => ['Banque',       'ri-bank-line',                '#2C3E50'],
                            'autre'        => ['Autre',        'ri-more-line',                '#888888'],
                        ];
                        $selectedMode = old('mode_paiement', $depense->mode_paiement ?? $modele->mode_paiement ?? 'cash');
                    @endphp

                    @foreach($modes as $val => [$label, $icon, $color])
                        <label style="cursor:pointer;">
                            <input type="radio" name="mode_paiement" value="{{ $val }}"
                                   {{ $selectedMode === $val ? 'checked' : '' }}
                                   style="display:none;" class="mode-radio"
                                   onchange="updateModeUI()">
                            <div class="mode-btn {{ $selectedMode === $val ? 'mode-selected' : '' }}"
                                 data-val="{{ $val }}"
                                 style="padding:12px;border-radius:9px;border:1px solid var(--noir-border);
                                        text-align:center;transition:all .18s;background:var(--bg-white);">
                                <i class="{{ $icon }}" style="font-size:22px;display:block;margin-bottom:5px;color:{{ $color }};"></i>
                                <span style="font-size:12px;font-weight:600;color:var(--noir-mid);">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- Référence mobile (Orange Money, Wave, MTN) --}}
                <div id="refMobileWrap" style="{{ in_array($selectedMode, ['orange_money','wave','mtn_momo']) ? '' : 'display:none;' }}">
                    <div class="form-group">
                        <label class="form-label">Référence de transaction *</label>
                        <input type="text" name="reference_mobile" class="form-control"
                               value="{{ old('reference_mobile', $depense->reference_mobile ?? '') }}"
                               placeholder="Ex: OM240322001234 (10-15 caractères)"
                               maxlength="20">
                        <div style="font-size:11px;color:var(--noir-light);margin-top:4px;">
                            <i class="ri-information-line"></i> Référence alphanumérique fournie par l'opérateur
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">Options supplémentaires</span>
            </div>
            <div class="card-body">

                {{-- Statut (validation optionnelle) --}}
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <div style="display:flex;gap:14px;margin-top:4px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border-radius:8px;border:1px solid var(--noir-border);background:var(--bg-page);">
                            <input type="radio" name="statut" value="validee"
                                   {{ old('statut', $depense->statut ?? 'validee') === 'validee' ? 'checked' : '' }}>
                            <span style="font-size:13px;font-weight:600;color:var(--succes);">
                                <i class="ri-check-double-line"></i> Validée directement
                            </span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border-radius:8px;border:1px solid var(--noir-border);background:var(--bg-page);">
                            <input type="radio" name="statut" value="brouillon"
                                   {{ old('statut', $depense->statut ?? '') === 'brouillon' ? 'checked' : '' }}>
                            <span style="font-size:13px;font-weight:600;color:var(--noir-mid);">
                                <i class="ri-draft-line"></i> Brouillon (à valider)
                            </span>
                        </label>
                    </div>
                    <div style="font-size:11px;color:var(--noir-light);margin-top:6px;">
                        <i class="ri-information-line"></i> La validation hiérarchique est optionnelle
                    </div>
                </div>

                <hr class="divider">

                {{-- Dépense récurrente --}}
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="checkbox" name="est_recurrente" value="1" id="estRecurrente"
                               {{ old('est_recurrente', $depense->est_recurrente ?? false) ? 'checked' : '' }}
                               onchange="toggleRecurrence()"
                               style="width:16px;height:16px;accent-color:var(--or);">
                        <span style="font-size:14px;font-weight:600;color:var(--noir-text);">
                            <i class="ri-repeat-line" style="color:var(--or);"></i>
                            Dépense récurrente (modèle cloneable)
                        </span>
                    </label>
                    <div style="font-size:11px;color:var(--noir-light);margin-top:4px;margin-left:26px;">
                        Marquer comme modèle pour pouvoir la cloner facilement (loyer, abonnements...)
                    </div>
                </div>

                <div id="recurrenceWrap" style="{{ old('est_recurrente', $depense->est_recurrente ?? false) ? '' : 'display:none;' }}">
                    <div class="form-group" style="max-width:300px;">
                        <label class="form-label">Fréquence</label>
                        <select name="frequence_recurrence" class="form-control">
                            <option value="">— Choisir —</option>
                            @foreach(['hebdomadaire'=>'Hebdomadaire','mensuelle'=>'Mensuelle','trimestrielle'=>'Trimestrielle','annuelle'=>'Annuelle'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('frequence_recurrence', $depense->frequence_recurrence ?? $modele->frequence_recurrence ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes / observations</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Informations complémentaires...">{{ old('notes', $depense->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;">
            <a href="{{ route('depenses.index') }}" class="btn btn-outline btn-lg">Annuler</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="ri-save-line"></i>
                {{ isset($depense) ? 'Mettre à jour' : 'Enregistrer la dépense' }}
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
const mobileModes = ['orange_money', 'wave', 'mtn_momo'];

function updateModeUI() {
    const radios = document.querySelectorAll('.mode-radio');
    let selected = '';
    radios.forEach(r => {
        const btn = document.querySelector(`.mode-btn[data-val="${r.value}"]`);
        if (r.checked) {
            selected = r.value;
            btn.style.borderColor = 'var(--or)';
            btn.style.background  = 'var(--or-pale)';
        } else {
            btn.style.borderColor = 'var(--noir-border)';
            btn.style.background  = 'var(--bg-white)';
        }
    });
    document.getElementById('refMobileWrap').style.display =
        mobileModes.includes(selected) ? 'block' : 'none';
}

function toggleRecurrence() {
    const checked = document.getElementById('estRecurrente').checked;
    document.getElementById('recurrenceWrap').style.display = checked ? 'block' : 'none';
}

// Init au chargement
document.querySelectorAll('.mode-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const val = btn.dataset.val;
        document.querySelector(`.mode-radio[value="${val}"]`).checked = true;
        updateModeUI();
    });
});
updateModeUI();
</script>
@endpush
