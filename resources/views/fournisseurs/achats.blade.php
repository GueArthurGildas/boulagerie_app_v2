@extends('layouts.app')
@section('title', 'Achats — ' . $fournisseur->nom)
@section('page-title', 'Nouvel Achat')
@section('page-subtitle', $fournisseur->nom)

@section('content')

<div class="page-header">
    <div>
        <h2>Nouvel Achat</h2>
        <div class="or-line"></div>
        <p style="margin-top:8px;">Fournisseur : <strong>{{ $fournisseur->nom }}</strong></p>
    </div>
    <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour fiche
    </a>
</div>

@if($fournisseur->plafond_credit > 0)
<div class="alert alert-warning" style="margin-bottom:20px;">
    <i class="ri-bank-line"></i>
    Crédit disponible : <strong>{{ number_format($fournisseur->credit_disponible) }} FCFA</strong>
    sur un plafond de {{ number_format($fournisseur->plafond_credit) }} FCFA
    ({{ $fournisseur->taux_endettement }}% utilisé)
</div>
@endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:22px;">

    <form method="POST" action="{{ route('fournisseurs.achat.store', $fournisseur) }}" id="formAchat">
        @csrf

        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><span class="card-title">Informations du bon</span></div>
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
                            @foreach(['cash'=>'Cash','orange_money'=>'Orange Money','wave'=>'Wave','mtn_momo'=>'MTN MoMo','banque'=>'Banque','credit'=>'Crédit (paiement différé)','autre'=>'Autre'] as $val => $label)
                                <option value="{{ $val }}" {{ old('mode_paiement','cash') === $val ? 'selected' : '' }}>{{ $label }}</option>
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

        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">Lignes de matières premières</span>
                <button type="button" class="btn btn-outline btn-sm" id="btnAjouterLigne">
                    <i class="ri-add-line"></i> Ajouter une ligne
                </button>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 110px 120px 100px 32px;gap:8px;margin-bottom:8px;">
                    <div class="form-label" style="margin:0;">Matière première</div>
                    <div class="form-label" style="margin:0;">Quantité</div>
                    <div class="form-label" style="margin:0;">Prix unit. (FCFA)</div>
                    <div class="form-label" style="margin:0;">Total</div>
                    <div></div>
                </div>

                <div id="lignesContainer">
                    <div class="achat-ligne" data-index="0">
                        <div style="display:grid;grid-template-columns:1fr 110px 120px 100px 32px;gap:8px;margin-bottom:8px;align-items:center;">
                            <select name="lignes[0][matiere_premiere_id]" class="form-control sel-matiere" required>
                                <option value="">— Choisir —</option>
                                @foreach($matieres as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }} ({{ $m->unite }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="lignes[0][quantite]" class="form-control inp-qte"
                                   step="0.001" min="0.001" placeholder="0" required>
                            <input type="number" name="lignes[0][prix_unitaire]" class="form-control inp-prix"
                                   step="1" min="1" placeholder="0" required>
                            <div class="total-ligne" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:var(--or);text-align:right;padding:10px 0;">
                                0
                            </div>
                            <button type="button" class="btn-remove-ligne" onclick="removeLigne(this)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="divider">
                <div style="display:flex;justify-content:flex-end;align-items:center;gap:12px;">
                    <span style="font-size:13px;color:var(--noir-mid);">Total bon d'achat :</span>
                    <span id="totalGeneral" style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;color:var(--noir-text);">0</span>
                    <span style="font-size:13px;color:var(--noir-light);">FCFA</span>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;">
            <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-outline btn-lg">Annuler</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="ri-save-line"></i> Enregistrer en brouillon
            </button>
        </div>
    </form>

    {{-- Panneau latéral --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">Aide</span></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:28px;height:28px;background:var(--warning-bg);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ri-draft-line" style="color:var(--warning);font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--noir-text);">Brouillon d'abord</div>
                            <div style="font-size:12px;color:var(--noir-light);">L'achat est enregistré sans toucher au stock.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:28px;height:28px;background:var(--succes-bg);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ri-check-double-line" style="color:var(--succes);font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--noir-text);">Validation = stock + dépense</div>
                            <div style="font-size:12px;color:var(--noir-light);">À la validation, le stock est mis à jour et une dépense est créée automatiquement.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div style="width:28px;height:28px;background:var(--info-bg);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ri-bank-line" style="color:var(--info);font-size:14px;"></i>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--noir-text);">Mode crédit</div>
                            <div style="font-size:12px;color:var(--noir-light);">Le solde dû du fournisseur est incrémenté. Soldé via un règlement.</div>
                        </div>
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

function recalculer() {
    let total = 0;
    document.querySelectorAll('.achat-ligne').forEach(ligne => {
        const qte   = parseFloat(ligne.querySelector('.inp-qte')?.value) || 0;
        const prix  = parseInt(ligne.querySelector('.inp-prix')?.value) || 0;
        const mont  = Math.round(qte * prix);
        ligne.querySelector('.total-ligne').textContent = new Intl.NumberFormat('fr-FR').format(mont);
        total += mont;
    });
    document.getElementById('totalGeneral').textContent = new Intl.NumberFormat('fr-FR').format(total);
}

function removeLigne(btn) {
    const lignes = document.querySelectorAll('.achat-ligne');
    if (lignes.length <= 1) return;
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
    const options = matieres.map(m => `<option value="${m.id}">${m.nom} (${m.unite})</option>`).join('');
    const div = document.createElement('div');
    div.className = 'achat-ligne';
    div.setAttribute('data-index', compteur);
    div.innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 110px 120px 100px 32px;gap:8px;margin-bottom:8px;align-items:center;">
            <select name="lignes[${compteur}][matiere_premiere_id]" class="form-control sel-matiere" required>
                <option value="">— Choisir —</option>${options}
            </select>
            <input type="number" name="lignes[${compteur}][quantite]" class="form-control inp-qte" step="0.001" min="0.001" placeholder="0" required>
            <input type="number" name="lignes[${compteur}][prix_unitaire]" class="form-control inp-prix" step="1" min="1" placeholder="0" required>
            <div class="total-ligne" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:var(--or);text-align:right;padding:10px 0;">0</div>
            <button type="button" class="btn-remove-ligne" onclick="removeLigne(this)"><i class="ri-delete-bin-line"></i></button>
        </div>
    `;
    document.getElementById('lignesContainer').appendChild(div);
    compteur++;
});

document.addEventListener('input', e => {
    if (e.target.classList.contains('inp-qte') || e.target.classList.contains('inp-prix')) recalculer();
});

document.getElementById('modeSelect').addEventListener('change', function() {
    document.getElementById('echeanceWrap').style.display = this.value === 'credit' ? 'block' : 'none';
});
</script>
@endpush
