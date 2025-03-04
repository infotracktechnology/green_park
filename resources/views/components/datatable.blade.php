@props([
    'columns' => [],
    'rows' => [],
    'actions' => [],
])

<div class="table-responsive">
    <table class="table table-striped table-sm" id="myTable">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach

                @if (count($actions))
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($columns as $column)
                        <td>
                            @if($column == 'Status')
                                @php
                                    $statusClass = match(strtolower($row['Status'])) {
                                        'scheduled' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">
                                    {{ $row['Status'] }}
                                </span>
                            @else
                                {{ $row[$column] ?? '' }}
                            @endif
                        </td>
                    @endforeach

                    @if (count($actions))
                        <td class="">
                            @foreach ($actions as $key => $action)
                                @php
                                    $icon = match(strtolower($key)) {
                                        'edit' => 'fas fa-edit',
                                        'delete' => 'fas fa-trash',
                                        'preview', 'show', 'view' => 'fas fa-eye',
                                        default => 'fas fa-link'
                                    };
                                    
                                    $btnClass = match(strtolower($key)) {
                                        'delete' => 'btn-danger',
                                        default => 'btn-primary'
                                    };
                                @endphp

                                @if(strtolower($key) == 'delete')
                                    <form action="{{ route($action, $row['id']) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn {{ $btnClass }}" title="{{ $key }}">
                                            <i class="{{ $icon }}"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route($action, $row['id']) }}" class="btn {{ $btnClass }}" title="{{ $key }}">
                                        <i class="{{ $icon }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>