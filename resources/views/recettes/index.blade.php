@extends('layouts.app')

@section('title', 'Recettes')
@section('page-title', 'Recettes')
@section('page-subtitle', 'Fiches techniques de production')

@section('content')

<div class="page-header">
    <div>
        <h2>Recettes</h2>
        <p>{{ $recettes->total() }} recette(s) enregistrée(s)</p>
    </div>
    <a href="{{ route('recettes.create') }}" class="btn btn-primary btn-lg">
        <i class="ri-add-circle-line"></i> Nouvelle recette
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Pièces attendues</th>
                    <th>Nb matières</th>
                    <th>Productions</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recettes as $recette)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-mid);font-size:12px;">#{{ $recette->id }}</td>
                        <td>
                            <div style="font-weight:600;color:var(--blanc);">{{ $recette->nom }}</div>
                            @if($recette->description)
                                <div style="font-size:12px;color:var(--gris-mid);margin-top:2px;">{{ Str::limit($recette->description, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--blanc);">
                                {{ $recette->nb_pieces_attendues }}
                            </span>
                            <span style="font-size:11px;color:var(--gris-mid);">pcs</span>
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $recette->lignes_count ?? $recette->lignes->count() }} matière(s)</span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-light);">
                            {{ $recette->productions_count ?? 0 }}
                        </td>
                        <td>
                            <span class="badge {{ $recette->actif ? 'badge-green' : 'badge-gray' }}">
                                {{ $recette->actif ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('recettes.show', $recette) }}" class="btn btn-outline btn-sm" title="Voir">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-outline btn-sm" title="Modifier">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                @if(!$recette->productions_count)
                                    <form method="POST" action="{{ route('recettes.destroy', $recette) }}"
                                          onsubmit="return confirm('Supprimer cette recette ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Supprimer">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:48px;color:var(--gris-dark);">
                            <i class="ri-book-2-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            Aucune recette créée
                            <br>
                            <a href="{{ route('recettes.create') }}" class="btn btn-primary" style="margin-top:16px;">
                                <i class="ri-add-line"></i> Créer une recette
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($recettes->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--noir-border);">
            {{ $recettes->links() }}
        </div>
    @endif
</div>

@endsection
