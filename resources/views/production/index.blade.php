@extends('layouts.app')

@section('title', 'Fournées')
@section('page-title', 'Fournées')
@section('page-subtitle', 'Gestion des sessions de production')

@section('content')

<div class="page-header">
    <div>
        <h2>Fournées</h2>
        <p>Historique et suivi des productions</p>
    </div>
    <a href="{{ route('productions.create') }}" class="btn btn-primary btn-lg">
        <i class="ri-add-circle-line"></i> Démarrer une fournée
    </a>
</div>

{{-- Filtres --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:150px;">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div style="flex:1;min-width:150px;">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control">
                    <option value="">Tous</option>
                    <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminee" {{ request('statut') === 'terminee' ? 'selected' : '' }}>Terminée</option>
                    <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            <div style="flex:1;min-width:150px;">
                <label class="form-label">Équipe</label>
                <select name="equipe" class="form-control">
                    <option value="">Toutes</option>
                    <option value="jour" {{ request('equipe') === 'jour' ? 'selected' : '' }}>Jour</option>
                    <option value="nuit" {{ request('equipe') === 'nuit' ? 'selected' : '' }}>Nuit</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="ri-search-line"></i> Filtrer</button>
                <a href="{{ route('productions.index') }}" class="btn btn-outline"><i class="ri-refresh-line"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Recette</th>
                    <th>Équipe</th>
                    <th>Pièces att.</th>
                    <th>Pièces prod.</th>
                    <th>Rendement</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productions as $p)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-mid);font-size:12px;">#{{ $p->id }}</td>
                        <td style="font-family:'DM Mono',monospace;font-size:13px;">{{ $p->date_production->format('d/m/Y') }}</td>
                        <td style="font-weight:600;color:var(--blanc)">{{ $p->recette->nom }}</td>
                        <td>
                            <span class="badge {{ $p->equipe === 'jour' ? 'badge-blue' : 'badge-gray' }}">
                                <i class="ri-{{ $p->equipe === 'jour' ? 'sun' : 'moon' }}-line"></i>
                                {{ ucfirst($p->equipe) }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-light)">{{ $p->nb_pieces_attendues }}</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--blanc)">
                            {{ $p->statut === 'en_cours' ? '—' : $p->nb_pieces_produites }}
                        </td>
                        <td>
                            @if($p->rendement > 0)
                                <div style="display:flex;align-items:center;gap:8px;min-width:100px;">
                                    <div class="progress" style="width:50px">
                                        <div class="progress-bar {{ $p->rendement >= 90 ? 'green' : ($p->rendement >= 70 ? 'orange' : '') }}"
                                             style="width:{{ min($p->rendement, 100) }}%"></div>
                                    </div>
                                    <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--gris-light)">
                                        {{ $p->rendement }}%
                                    </span>
                                </div>
                            @else
                                <span style="color:var(--gris-dark)">—</span>
                            @endif
                        </td>
                        <td>
                            @if($p->statut === 'terminee')
                                <span class="badge badge-green"><i class="ri-check-double-line"></i> Terminée</span>
                            @elseif($p->statut === 'en_cours')
                                <span class="badge badge-orange"><i class="ri-loader-line"></i> En cours</span>
                            @else
                                <span class="badge badge-red"><i class="ri-close-line"></i> Annulée</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('productions.show', $p) }}" class="btn btn-outline btn-sm" title="Détails">
                                    <i class="ri-eye-line"></i>
                                </a>
                                @if($p->statut === 'en_cours')
                                    <a href="{{ route('productions.show', $p) }}" class="btn btn-success btn-sm" title="Clôturer">
                                        <i class="ri-check-line"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:48px;color:var(--gris-dark);">
                            <i class="ri-fire-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            Aucune fournée trouvée
                            <br>
                            <a href="{{ route('productions.create') }}" class="btn btn-primary" style="margin-top:16px;">
                                <i class="ri-add-line"></i> Démarrer une fournée
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($productions->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--noir-border);">
            <div class="pagination">
                {{ $productions->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif
</div>

@endsection
