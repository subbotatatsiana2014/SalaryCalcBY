<div class="tree-node" data-department-id="{{ $department->id }}" style="margin-left: {{ ($level ?? 0) * 20 }}px;">
    <div class="node-content" onclick="toggleTreeNode({{ $department->id }})">
        <div class="department-info">
            <div class="department-color" style="background: {{ $department->color ?? '#3B82F6' }}"></div>
            <div class="department-details">
                <div class="department-name">
                    <strong>{{ $department->name }}</strong>
                    <code class="department-code">{{ $department->code }}</code>
                </div>
                <div class="department-meta">
                    <span class="employees-count">
                        <i class="fas fa-users"></i> {{ $department->employees_count ?? 0 }} сотрудников
                    </span>
                    <span class="budget-info">
                        <i class="fas fa-wallet"></i> {{ number_format($department->budget, 2) }} BYN
                    </span>
                    @if($department->manager)
                        <span class="manager-info">
                            <i class="fas fa-user-tie"></i> {{ $department->manager->name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="node-actions">
                <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); editDepartment({{ $department->id }})"
                        title="Редактировать">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); toggleTreeNode({{ $department->id }})"
                        title="Развернуть/свернуть">
                    <i class="fas fa-chevron-down" id="toggle-icon-{{ $department->id }}"></i>
                </button>
            </div>
        </div>
    </div>

    @if($department->children && $department->children->count() > 0)
        <div class="node-children" id="node-children-{{ $department->id }}">
            @foreach($department->children as $child)
                @include('departments.partials.tree-node', ['department' => $child, 'level' => ($level ?? 0) + 1])
            @endforeach
        </div>
    @endif
</div>
