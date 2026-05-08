@extends('layouts.app')

@section('title', 'Пользователи системы - SalaryCalc BY')

@section('content')
    <div class="page">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> Пользователи системы</h3>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Добавить пользователя
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Пользователь</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Телефон</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar-small">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $user->name }}</div>
                                            @if($user->employee)
                                                <small class="text-muted">{{ $user->employee->position ?? '' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'info') }}">
                                    {{ \App\Models\User::getAvailableRoles()[$user->role] ?? $user->role }}
                                </span>
                                </td>
                                <td>{{ $user->phone ?? '—' }}</td>
                                <td>{{ $user->created_at ? $user->created_at->format('d.m.Y') : '—' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline btn-sm" title="Просмотр">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline btn-sm" title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить пользователя {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    Пользователи не найдены
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="pagination">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
