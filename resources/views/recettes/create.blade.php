@extends('layouts.app')

@section('title', isset($recette) ? 'Modifier recette' : 'Nouvelle recette')
@section('page-title', isset($recette) ? 'Modifier la recette' : 'Nouvelle recette')

@push('styles')
<style>
.ligne-ingrediant {
    display: grid;
    grid-template-columns: 1fr 120px 36px;
    gap: 10px;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--noir-border);
}
.ligne-ingrediant:last-child { border-bottom: none; }

.btn-remove-ligne {
    width: 32px; height: 32px;
    background: rgba(192,57,43,.1);
    border: 1px solid rgba(192,57,43,.3);
    border-radius: 6px;
    color: var(--rouge-vif);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    transition: all .2s;
    flex-shrink: 0;
}
.btn-remove-ligne:hover { background: var(--rouge); color: var(--blanc); }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2>{{ isset($recette) ? 'Modifier : ' . $recette->nom : 'Nouvelle Recette' }}</h2>
        <p>{{ isset($recette) ? 'Modifiez la fiche technique' : 'Créez une fiche technique de production' }}</p>
    </div>
    <a href="{{ route('recettes.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour
    </a>
</div>

<form method="POST" action="{{ isset($recette) ? route('recettes.update', $recette) : route('recettes.store') }}">
    @csrf
    @if(isset($recette)) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

        {{-- Infos générales --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Informations générales</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Nom de la recette *</label>
                    <input type="text" name="nom" class="form-control"
                           value="{{ old('nom', $recette->nom ?? '') }}"
                           placeholder="Ex: Pain baguette 250g" required>
                    @error('nom')
                        <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Pièces attendues par fournée *</label>
                    <input type="number" name="nb_pieces_attendues" class="form-control"
                           value="{{ old('nb_pieces_attendues', $recette->nb_pieces_attendues ?? 1) }}"
                           min="1" required>
                    @error('nb_pieces_attendues')
                        <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description (optionnel)</label>
                    <textarea name="description" class="form-control" rows="4"
                              placeholder="Notes sur la recette, instructions particulières...">{{ old('description', $recette->description ?? '') }}</textarea>
                </div>

                @if(isset($recette))
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <div style="display:flex;gap:16px;margin-top:4px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="radio" name="actif" value="1" {{ old('actif', $recette->actif ?? 1) ? 'checked' : '' }}>
                            <span style="font-size:14px;color:var(--blanc);">Active</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="radio" name="actif" value="0" {{ !old('actif', $recette->actif ?? 1) ? 'checked' : '' }}>
                            <span style="font-size:14px;color:var(--gris-light);">Inactive</span>
                        </label>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Composition --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Composition (matières premières)</span>
                <button type="button" class="btn btn-outline btn-sm" id="btnAjouter">
                    <i class="ri-add-line"></i> Ajouter
                </button>
            </div>
            <div class="card-body">
                @error('lignes')
                    <div class="alert alert-error" style="margin-bottom:16px;">
                        <i class="ri-error-warning-line"></i> {{ $message }}
                    </div>
                @enderror

                <div style="display:grid;grid-template-columns:1fr 120px 36px;gap:10px;margin-bottom:8px;">
                    <div class="form-label" style="margin:0;">Matière première</div>
                    <div class="form-label" style="margin:0;">Quantité</div>
                    <div></div>
                </div>

                <div id="lignesContainer">
                    @if(isset($recette) && $recette->lignes->count())
                        @foreach($recette->lignes as $i => $ligne)
                            <div class="ligne-ingrediant">
                                <select name="lignes[{{ $i }}][matiere_premiere_id]" class="form-control" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($matieres as $m)
                                        <option value="{{ $m->id }}" {{ $ligne->matiere_premiere_id == $m->id ? 'selected' : '' }}>
                                            {{ $m->nom }} ({{ $m->unite }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" name="lignes[{{ $i }}][quantite]" class="form-control"
                                       step="0.001" min="0.001" value="{{ $ligne->quantite }}" required>
                                <button type="button" class="btn-remove-ligne" onclick="this.closest('.ligne-ingrediant').remove(); reindexer();">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        {{-- Ligne vide initiale --}}
                        <div class="ligne-ingrediant">
                            <select name="lignes[0][matiere_premiere_id]" class="form-control" required>
                                <option value="">— Choisir —</option>
                                @foreach($matieres as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }} ({{ $m->unite }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="lignes[0][quantite]" class="form-control"
                                   step="0.001" min="0.001" placeholder="0.000" required>
                            <button type="button" class="btn-remove-ligne" onclick="this.closest('.ligne-ingrediant').remove(); reindexer();">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div style="margin-top:16px;padding:10px 14px;background:var(--noir-border);border-radius:8px;font-size:12px;color:var(--gris-mid);">
                    <i class="ri-information-line"></i>
                    Les quantités représentent ce qui sera consommé pour <strong style="color:var(--blanc)">une fournée complète</strong>.
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;">
        <a href="{{ route('recettes.index') }}" class="btn btn-outline btn-lg">Annuler</a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="ri-save-line"></i>
            {{ isset($recette) ? 'Mettre à jour' : 'Créer la recette' }}
        </button>
    </div>

</form>

{{-- Template JSON pour les matieres --}}
<script>
const matieres = @json($matieres->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom, 'unite' => $m->unite]));

let compteur = {{ isset($recette) ? $recette->lignes->count() : 1 }};

function reindexer() {
    document.querySelectorAll('#lignesContainer .ligne-ingrediant').forEach((row, i) => {
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
    compteur = document.querySelectorAll('#lignesContainer .ligne-ingrediant').length;
}

document.getElementById('btnAjouter').addEventListener('click', function() {
    const container = document.getElementById('lignesContainer');
    const options = matieres.map(m => `<option value="${m.id}">${m.nom} (${m.unite})</option>`).join('');

    const div = document.createElement('div');
    div.className = 'ligne-ingrediant';
    div.innerHTML = `
        <select name="lignes[${compteur}][matiere_premiere_id]" class="form-control" required>
            <option value="">— Choisir —</option>
            ${options}
        </select>
        <input type="number" name="lignes[${compteur}][quantite]" class="form-control"
               step="0.001" min="0.001" placeholder="0.000" required>
        <button type="button" class="btn-remove-ligne" onclick="this.closest('.ligne-ingrediant').remove(); reindexer();">
            <i class="ri-delete-bin-line"></i>
        </button>
    `;
    container.appendChild(div);
    compteur++;
});
</script>
@endsection
