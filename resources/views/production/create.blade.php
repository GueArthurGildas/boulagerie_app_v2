@extends('layouts.app')

@section('title', 'Nouvelle fournée')
@section('page-title', 'Nouvelle Fournée')
@section('page-subtitle', 'Démarrer une session de production')

@push('styles')
<style>
    .matiere-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 12px;
        align-items: center;
        padding: 10px 14px;
        border-radius: 8px;
        background: var(--noir-border);
        margin-bottom: 8px;
        transition: background .2s;
    }
    .matiere-row.ok     { border-left: 3px solid var(--succes); }
    .matiere-row.bas    { border-left: 3px solid var(--warning); }
    .matiere-row.manque { border-left: 3px solid var(--rouge-vif); }

    .matiere-name { font-weight: 600; font-size: 14px; color: var(--blanc); }
    .matiere-qty  { font-family: 'DM Mono', monospace; font-size: 13px; }

    .recette-preview {
        background: var(--noir-border);
        border-radius: 10px;
        padding: 20px;
        margin-top: 16px;
        display: none;
    }
    .recette-preview.visible { display: block; }

    .preview-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 16px;
        letter-spacing: 1px;
        color: var(--gris-mid);
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .dispo-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-weight: 600;
        font-size: 14px;
    }
    .dispo-ok      { background: rgba(39,174,96,.15); color: #2ecc71; border: 1px solid rgba(39,174,96,.3); }
    .dispo-ko      { background: rgba(192,57,43,.15); color: var(--rouge-vif); border: 1px solid rgba(192,57,43,.3); }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2>Nouvelle Fournée</h2>
        <p>Sélectionnez une recette pour démarrer la production</p>
    </div>
    <a href="{{ route('productions.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

    {{-- Formulaire --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Paramètres de la fournée</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('productions.store') }}" id="formFournee">
                @csrf

                <div class="form-group">
                    <label class="form-label">Recette *</label>
                    <select name="recette_id" id="recette_id" class="form-control" required>
                        <option value="">— Choisir une recette —</option>
                        @foreach($recettes as $recette)
                            <option value="{{ $recette->id }}" {{ old('recette_id') == $recette->id ? 'selected' : '' }}>
                                {{ $recette->nom }} ({{ $recette->nb_pieces_attendues }} pcs attendues)
                            </option>
                        @endforeach
                    </select>
                    @error('recette_id')
                        <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Équipe *</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <label style="cursor:pointer;">
                            <input type="radio" name="equipe" value="jour" {{ old('equipe','jour') === 'jour' ? 'checked' : '' }} style="display:none;" id="equipe_jour">
                            <div class="equipe-btn" id="btn_jour" onclick="selectEquipe('jour')"
                                 style="padding:14px;border-radius:8px;border:2px solid var(--rouge);background:rgba(192,57,43,.1);text-align:center;transition:all .2s;">
                                <i class="ri-sun-line" style="font-size:24px;display:block;margin-bottom:4px;color:var(--warning);"></i>
                                <span style="font-weight:600;color:var(--blanc);">Équipe Jour</span>
                            </div>
                        </label>
                        <label style="cursor:pointer;">
                            <input type="radio" name="equipe" value="nuit" {{ old('equipe') === 'nuit' ? 'checked' : '' }} style="display:none;" id="equipe_nuit">
                            <div class="equipe-btn" id="btn_nuit" onclick="selectEquipe('nuit')"
                                 style="padding:14px;border-radius:8px;border:2px solid var(--noir-border);background:transparent;text-align:center;transition:all .2s;">
                                <i class="ri-moon-line" style="font-size:24px;display:block;margin-bottom:4px;color:var(--gris-mid);"></i>
                                <span style="font-weight:600;color:var(--gris-light);">Équipe Nuit</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (optionnel)</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Observations, consignes particulières...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;" id="btnSubmit" disabled>
                    <i class="ri-fire-line"></i> Démarrer la fournée
                </button>

            </form>
        </div>
    </div>

    {{-- Prévisualisation stock --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">Disponibilité des matières</span>
                <span id="loadingBadge" style="display:none;" class="badge badge-gray">
                    <i class="ri-loader-line"></i> Vérification...
                </span>
            </div>
            <div class="card-body">
                <div id="stockPreview" style="color:var(--gris-dark);text-align:center;padding:32px 0;">
                    <i class="ri-stack-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                    Sélectionnez une recette pour voir<br>les matières nécessaires
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function selectEquipe(val) {
        const jour = document.getElementById('btn_jour');
        const nuit = document.getElementById('btn_nuit');
        document.getElementById('equipe_jour').checked = val === 'jour';
        document.getElementById('equipe_nuit').checked = val === 'nuit';

        if (val === 'jour') {
            jour.style.borderColor = 'var(--rouge)';
            jour.style.background = 'rgba(192,57,43,.1)';
            jour.querySelector('span').style.color = 'var(--blanc)';
            nuit.style.borderColor = 'var(--noir-border)';
            nuit.style.background = 'transparent';
            nuit.querySelector('span').style.color = 'var(--gris-light)';
        } else {
            nuit.style.borderColor = 'var(--rouge)';
            nuit.style.background = 'rgba(192,57,43,.1)';
            nuit.querySelector('span').style.color = 'var(--blanc)';
            jour.style.borderColor = 'var(--noir-border)';
            jour.style.background = 'transparent';
            jour.querySelector('span').style.color = 'var(--gris-light)';
        }
    }

    document.getElementById('recette_id').addEventListener('change', function () {
        const id = this.value;
        const preview = document.getElementById('stockPreview');
        const loading = document.getElementById('loadingBadge');
        const btn = document.getElementById('btnSubmit');

        if (!id) {
            preview.innerHTML = `<i class="ri-stack-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>Sélectionnez une recette`;
            btn.disabled = true;
            return;
        }

        loading.style.display = 'inline-flex';
        preview.innerHTML = '<div style="text-align:center;padding:24px;color:var(--gris-mid);">Vérification en cours...</div>';

        fetch(`/api/recettes/${id}/stock`)
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';

                if (data.ok) {
                    btn.disabled = false;
                    let html = `<div class="dispo-ok"><i class="ri-check-double-line"></i> Toutes les matières sont disponibles</div>`;
                    html += data.lignes ? renderLignes(data.lignes) : '';
                    preview.innerHTML = html;
                } else {
                    btn.disabled = true;
                    let html = `<div class="dispo-ko"><i class="ri-error-warning-line"></i> Stock insuffisant pour démarrer</div>`;
                    html += `<div style="font-size:12px;color:var(--gris-mid);margin-bottom:10px;">Matières manquantes :</div>`;
                    data.manquants.forEach(m => {
                        html += `<div class="matiere-row manque">
                            <div>
                                <div class="matiere-name">${m.matiere}</div>
                                <div style="font-size:12px;color:var(--gris-mid)">Requis : ${m.requis} ${m.unite}</div>
                            </div>
                            <div class="matiere-qty" style="color:var(--rouge-vif)">Dispo : ${m.disponible} ${m.unite}</div>
                            <span class="badge badge-red">-${m.manque} ${m.unite}</span>
                        </div>`;
                    });
                    preview.innerHTML = html;
                }
            })
            .catch(() => {
                loading.style.display = 'none';
                preview.innerHTML = '<div class="alert alert-error"><i class="ri-wifi-off-line"></i> Erreur de vérification</div>';
                btn.disabled = false;
            });
    });
</script>
@endpush
