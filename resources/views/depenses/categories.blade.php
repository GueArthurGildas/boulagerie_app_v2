@extends('layouts.app')

@section('title', 'Catégories de dépenses')
@section('page-title', 'Catégories')
@section('page-subtitle', 'Gestion des catégories de dépenses')

@section('content')

<div class="page-header">
    <div>
        <h2>Catégories de dépenses</h2>
        <div class="or-line"></div>
    </div>
    <a href="{{ route('depenses.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour aux dépenses
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">

    {{-- Liste des catégories --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Catégories existantes</span>
            <span class="badge badge-gray">{{ $categories->count() }}</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Couleur</th>
                        <th>Nom</th>
                        <th>Dépenses</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>
                                <span style="display:inline-block;width:22px;height:22px;border-radius:6px;background:{{ $cat->couleur }};border:1px solid var(--noir-border);"></span>
                            </td>
                            <td style="font-weight:600;color:var(--noir-text);">{{ $cat->nom }}</td>
                            <td>
                                <span class="badge badge-gray">{{ $cat->depenses_count }}</span>
                            </td>
                            <td>
                                @if(!$cat->depenses_count)
                                    <form method="POST" action="{{ route('depenses.categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Supprimer cette catégorie ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:12px;color:var(--noir-light);">En usage</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:32px;color:var(--noir-light);">
                                Aucune catégorie créée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Formulaire nouvelle catégorie --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Nouvelle catégorie</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('depenses.categories.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control"
                           value="{{ old('nom') }}"
                           placeholder="Ex: Loyer, Carburant, Salaires..." required>
                    @error('nom')
                        <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Couleur</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="color" name="couleur" value="{{ old('couleur', '#C8960C') }}"
                               style="width:48px;height:38px;border-radius:8px;border:1px solid var(--noir-border);cursor:pointer;padding:2px;">
                        <span style="font-size:12px;color:var(--noir-light);">Choisissez une couleur pour identifier la catégorie</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="ri-add-line"></i> Créer la catégorie
                </button>
            </form>

            <hr class="divider">

            {{-- Catégories suggérées --}}
            <div class="form-label">Suggestions rapides</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                @foreach([
                    ['Loyer', '#2C3E50'], ['Carburant', '#E67E22'], ['Salaires', '#8E44AD'],
                    ['Électricité', '#F39C12'], ['Eau', '#3498DB'], ['Internet', '#1ABC9C'],
                    ['Fournitures', '#95A5A6'], ['Transport', '#E74C3C'], ['Maintenance', '#D35400'],
                    ['Communication', '#2980B9'], ['Taxes', '#7F8C8D'], ['Divers', '#BDC3C7'],
                ] as [$nom, $couleur])
                    <button type="button" onclick="fillCategorie('{{ $nom }}', '{{ $couleur }}')"
                            style="display:flex;align-items:center;gap:6px;padding:5px 10px;border-radius:6px;
                                   border:1px solid var(--noir-border);background:var(--bg-white);
                                   cursor:pointer;font-size:12px;font-weight:500;color:var(--noir-mid);
                                   transition:all .18s;"
                            onmouseenter="this.style.borderColor='{{ $couleur }}'"
                            onmouseleave="this.style.borderColor='var(--noir-border)'">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $couleur }};"></span>
                        {{ $nom }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function fillCategorie(nom, couleur) {
    document.querySelector('input[name="nom"]').value = nom;
    document.querySelector('input[name="couleur"]').value = couleur;
}
</script>
@endpush
