@extends('layouts.app')
@section('title', 'Achats — ' . $fournisseur->nom)
@section('page-title', 'Nouvel Achat')
@section('page-subtitle', $fournisseur->nom)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>Nouvel Achat</h2>
        <div class="title-bar">
            <div class="title-bar-line"></div>
            <span class="title-bar-text">{{ $fournisseur->nom }}</span>
        </div>
    </div>
    <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-back">
        <i class="ri-arrow-left-line"></i> Retour fiche
    </a>
</div>

@if($fournisseur->plafond_credit > 0)
<div class="alert alert-warning" style="margin-bottom:20px;">
    <i class="ri-bank-line"></i>
    Crédit disponible : <strong class="montant">{{ number_format($fournisseur->credit_disponible) }}</strong> FCFA
    sur un plafond de <span class="montant">{{ number_format($fournisseur->plafond_credit) }}</span> FCFA
    ({{ $fournisseur->taux_endettement }}% utilisé)
</div>
@endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:22px;">

    <form method="POST" action="{{ route('fournisseurs.achat.store', $fournisseur) }}" id="formAchat">
        @csrf

        {{-- ── Informations du bon ── --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-title-icon gold"><i class="ri-file-list-3-line"></i></div>
                    <div>
                        <div class="section-title-text">Informations du bon</div>
                        <div class="section-title-sub">Référence, date, mode de paiement</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">N° BL / Référence facture</label>
                        <input type="text" name="reference" class="form-control"
                               value="{{ old('reference') }}" placeholder="Ex: BL-2024-001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date d'achat *</label>
                        <input type="date" name="date_achat" class="form-control"
                               value="{{ old('date_achat', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mode de paiement *</label>
                        <select name="mode_paiement" class="form-control" required id="modeSelect">
                            @foreach([
                                'cash'         => 'Cash',
                                'orange_money' => 'Orange Money',
                                'wave'         => 'Wave',
                                'mtn_momo'     => 'MTN MoMo',
                                'banque'       => 'Banque',
                                'credit'       => 'Crédit (paiement différé)',
                                'autre'        => 'Autre',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ old('mode_paiement','cash') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="echeanceWrap" style="{{ old('mode_paiement') === 'credit' ? '' : 'display:none;' }}">
                        <label class="form-label">Date d'échéance *</label>
                        <input type="date" name="date_echeance" class="form-control"
                               value="{{ old('date_echeance') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Conditions, observations...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Lignes matières + montants ── --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-title-icon gold"><i class="ri-stack-line"></i></div>
                    <div>
                        <div class="section-title-text">Matières premières achetées</div>
                        <div class="section-title-sub">Quantité × Prix unitaire = Total ligne</div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" id="btnAjouterLigne">
                    <i class="ri-add-line"></i> Ajouter une ligne
                </button>
            </div>
            <div class="card-body">

                {{-- En-têtes colonnes --}}
                <div style="display:grid;grid-template-columns:1fr 110px 130px 120px 32px;gap:8px;margin-bottom:10px;padding-bottom:10px;border-bottom:2px solid var(--noir-border);">
                    <div class="form-label" style="margin:0;">Matière première</div>
                    <div class="form-label" style="margin:0;">Quantité</div>
                    <div class="form-label" style="margin:0;">
                        Prix unit. <span style="color:var(--or);font-weight:900;">(FCFA) *</span>
                    </div>
                    <div class="form-label" style="margin:0;text-align:right;">Total ligne</div>
                    <div></div>
                </div>

                <div id="lignesContainer">
                    <div class="achat-ligne" data-index="0">
                        <div style="display:grid;grid-template-columns:1fr 110px 130px 120px 32px;gap:8px;margin-bottom:8px;align-items:center;">
                            <select name="lignes[0][matiere_premiere_id]" class="form-control sel-matiere" required>
                                <option value="">— Choisir —</option>
                                @foreach($matieres as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }} ({{ $m->unite }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="lignes[0][quantite]"
                                   class="form-control inp-qte"
                                   step="0.001" min="0.001" placeholder="0.000" required>
                            <div style="position:relative;">
                                <input type="number" name="lignes[0][prix_unitaire]"
                                       class="form-control inp-prix"
                                       step="1" min="1" placeholder="0" required
                                       style="padding-right:50px;">
                                <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                             font-size:10px;font-weight:700;color:var(--or);letter-spacing:.5px;
                                             pointer-events:none;">FCFA</span>
                            </div>
                            <div style="text-align:right;">
                                <div class="total-ligne montant"
                                     style="font-size:15px;font-weight:700;color:var(--or);padding:10px 0;">
                                    0
                                </div>
                                <div style="font-size:10px;color:var(--noir-light);margin-top:-4px;">FCFA</div>
                            </div>
                            <button type="button" class="btn-remove-ligne" onclick="removeLigne(this)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                {{-- Total général --}}
                <div style="display:flex;justify-content:flex-end;align-items:flex-end;gap:16px;">
                    <div style="text-align:right;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--noir-light);margin-bottom:4px;">
                            Total bon d'achat
                        </div>
                        <div style="display:flex;align-items:baseline;gap:6px;">
                            <span id="totalGeneral" class="montant-xl" style="color:var(--noir-text);">0</span>
                            <span style="font-size:13px;font-weight:700;color:var(--noir-light);letter-spacing:.5px;">FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;">
            <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-outline btn-lg">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary btn-xl">
                <i class="ri-save-line"></i> Enregistrer en brouillon
            </button>
        </div>
    </form>

    {{-- Panneau aide --}}
    <div>
        <div class="card">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-title-icon blue"><i class="ri-lightbulb-line"></i></div>
                    <div><div class="section-title-text">Comment ça marche</div></div>
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:28px;height:28px;background:var(--warning-bg);border:1px solid var(--warning-border);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;color:var(--warning);">1</span>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--noir-text);">Brouillon</div>
                            <div style="font-size:12px;color:var(--noir-light);line-height:1.5;">Stock non touché. Vous pouvez annuler à tout moment.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:28px;height:28px;background:var(--succes-bg);border:1px solid var(--succes-border);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;color:var(--succes);">2</span>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--noir-text);">Validation</div>
                            <div style="font-size:12px;color:var(--noir-light);line-height:1.5;">Le stock des matières est mis à jour et une dépense est créée automatiquement.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:28px;height:28px;background:var(--info-bg);border:1px solid var(--info-border);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-family:var(--font-mono);font-size:11px;font-weight:700;color:var(--info);">3</span>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--noir-text);">Règlement (crédit)</div>
                            <div style="font-size:12px;color:var(--noir-light);line-height:1.5;">Si paiement différé, effectuez un règlement depuis la fiche fournisseur.</div>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                <div style="background:var(--or-pale);border:1px solid var(--or-border);border-radius:8px;padding:12px;">
                    <div style="font-size:11px;font-weight:700;color:var(--or-dark);margin-bottom:4px;">
                        <i class="ri-information-line"></i> Prix unitaire obligatoire
                    </div>
                    <div style="font-size:12px;color:var(--or-dark);line-height:1.5;">
                        Saisissez le prix en <strong>FCFA</strong> pour chaque matière.
                        Ce prix sert à calculer le Prix Moyen Pondéré (PMP) du stock.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const matieres = @json($matieres->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom, 'unite' => $m->unite]));
let compteur = 1;

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(n));
}

function recalculer() {
    let total = 0;
    document.querySelectorAll('.achat-ligne').forEach(ligne => {
        const qte  = parseFloat(ligne.querySelector('.inp-qte')?.value) || 0;
        const prix = parseInt(ligne.querySelector('.inp-prix')?.value) || 0;
        const mont = Math.round(qte * prix);
        ligne.querySelector('.total-ligne').textContent = formatFcfa(mont);
        total += mont;
    });
    document.getElementById('totalGeneral').textContent = formatFcfa(total);
}

function makeLigneHTML(i) {
    const options = matieres.map(m =>
        `<option value="${m.id}">${m.nom} (${m.unite})</option>`
    ).join('');

    return `
    <div class="achat-ligne" data-index="${i}">
        <div style="display:grid;grid-template-columns:1fr 110px 130px 120px 32px;gap:8px;margin-bottom:8px;align-items:center;">
            <select name="lignes[${i}][matiere_premiere_id]" class="form-control sel-matiere" required>
                <option value="">— Choisir —</option>${options}
            </select>
            <input type="number" name="lignes[${i}][quantite]"
                   class="form-control inp-qte"
                   step="0.001" min="0.001" placeholder="0.000" required>
            <div style="position:relative;">
                <input type="number" name="lignes[${i}][prix_unitaire]"
                       class="form-control inp-prix"
                       step="1" min="1" placeholder="0" required
                       style="padding-right:50px;">
                <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                             font-size:10px;font-weight:700;color:var(--or);letter-spacing:.5px;
                             pointer-events:none;">FCFA</span>
            </div>
            <div style="text-align:right;">
                <div class="total-ligne montant" style="font-size:15px;font-weight:700;color:var(--or);padding:10px 0;">0</div>
                <div style="font-size:10px;color:var(--noir-light);margin-top:-4px;">FCFA</div>
            </div>
            <button type="button" class="btn-remove-ligne" onclick="removeLigne(this)">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    </div>`;
}

function removeLigne(btn) {
    if (document.querySelectorAll('.achat-ligne').length <= 1) return;
    btn.closest('.achat-ligne').remove();
    reindex();
    recalculer();
}

function reindex() {
    document.querySelectorAll('.achat-ligne').forEach((ligne, i) => {
        ligne.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
}

document.getElementById('btnAjouterLigne').addEventListener('click', () => {
    document.getElementById('lignesContainer').insertAdjacentHTML('beforeend', makeLigneHTML(compteur++));
});

document.addEventListener('input', e => {
    if (e.target.classList.contains('inp-qte') || e.target.classList.contains('inp-prix')) {
        recalculer();
    }
});

document.getElementById('modeSelect').addEventListener('change', function() {
    document.getElementById('echeanceWrap').style.display =
        this.value === 'credit' ? 'block' : 'none';
});
</script>
@endpush
