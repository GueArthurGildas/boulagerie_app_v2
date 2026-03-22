@extends('layouts.app')

@section('title', 'Matières premières')
@section('page-title', 'Matières premières')
@section('page-subtitle', 'Gestion des stocks de matières')

@section('content')

<div class="page-header">
    <div>
        <h2>Matières premières</h2>
        <p>{{ $matieres->total() }} matière(s) enregistrée(s)</p>
    </div>
    <a href="{{ route('matieres-premieres.create') }}" class="btn btn-primary btn-lg">
        <i class="ri-add-circle-line"></i> Nouvelle matière
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Unité</th>
                    <th>Stock actuel</th>
                    <th>Seuil min.</th>
                    <th>PMP (FCFA)</th>
                    <th>Péremption</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matieres as $m)
                    @php
                        $ratio = $m->stock_minimum > 0 ? ($m->stock_actuel / $m->stock_minimum) : 2;
                        $etat  = $m->stock_actuel <= 0 ? 'rupture' : ($ratio < 1 ? 'bas' : 'ok');
                    @endphp
                    <tr>
                        <td style="font-weight:600;color:var(--blanc);">{{ $m->nom }}</td>
                        <td style="color:var(--gris-mid);">{{ $m->unite }}</td>
                        <td>
                            <div class="stock-indicator">
                                <span class="stock-dot {{ $etat === 'ok' ? 'ok' : ($etat === 'bas' ? 'bas' : 'critique') }}"></span>
                                <span style="font-family:'DM Mono',monospace;font-size:13px;color:var(--blanc);">
                                    {{ $m->stock_actuel }}
                                </span>
                            </div>
                        </td>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-mid);">{{ $m->stock_minimum }}</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-light);">
                            {{ number_format($m->prix_moyen_pondere) }}
                        </td>
                        <td>
                            @if($m->date_peremption)
                                <span style="font-family:'DM Mono',monospace;font-size:12px;
                                    color:{{ $m->isPerime() ? 'var(--rouge-vif)' : ($m->isPerimeSoon() ? 'var(--warning)' : 'var(--gris-light)') }}">
                                    {{ $m->date_peremption->format('d/m/Y') }}
                                    @if($m->isPerime())
                                        <span class="badge badge-red" style="margin-left:4px;">Périmé</span>
                                    @elseif($m->isPerimeSoon())
                                        <span class="badge badge-orange" style="margin-left:4px;">Bientôt</span>
                                    @endif
                                </span>
                            @else
                                <span style="color:var(--gris-dark)">—</span>
                            @endif
                        </td>
                        <td>
                            @if($etat === 'rupture')
                                <span class="badge badge-red">Rupture</span>
                            @elseif($etat === 'bas')
                                <span class="badge badge-orange">Stock bas</span>
                            @else
                                <span class="badge badge-green">Normal</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('matieres-premieres.edit', $m) }}" class="btn btn-outline btn-sm" title="Modifier">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <form method="POST" action="{{ route('matieres-premieres.destroy', $m) }}"
                                      onsubmit="return confirm('Supprimer cette matière ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Supprimer">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:48px;color:var(--gris-dark);">
                            <i class="ri-stack-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            Aucune matière première enregistrée
                            <br>
                            <a href="{{ route('matieres-premieres.create') }}" class="btn btn-primary" style="margin-top:16px;">
                                <i class="ri-add-line"></i> Ajouter une matière
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($matieres->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--noir-border);">
            {{ $matieres->links() }}
        </div>
    @endif
</div>

@endsection
