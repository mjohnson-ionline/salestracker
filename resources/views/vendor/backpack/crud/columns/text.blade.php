{{-- regular object attribute --}}

@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['limit'] = $column['limit'] ?? 32;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['text'] = $column['default'] ?? '-';

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    if(is_array($column['value'])) {
        $column['value'] = json_encode($column['value']);
    }

    if(!empty($column['value'])) {
        $column['text'] = $column['prefix'].Str::limit($column['value'], $column['limit'], '…').$column['suffix'];
    }

@endphp
@includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
@if($column['escaped'])
    @php
        // if the $column['text'] contains the string https:// or http://, then we will turn the content into a link
        if ($column['name'] == 'canva_link') {
            echo $column['text'] = '<a href="'.$column['value'].'" target="_blank" class="btn btn-sm btn-link"><i class="la la-book"></i> View</a>';
        } elseif ($column['name'] == 'external_link') {

             echo $column['text'] = '<span class="copy_to_clip_list" data-clipboard-text="' . config('app.url') . '/sign/' . $entry->id . '/' . $entry->token . '" style="cursor:pointer;background-color:#467FD0;font-size:13px;padding:5px;border-radius:5px;color:white;padding-left:15px;padding-right:15px;">Copy</span>';
        } else {
            echo e($column['text']);
        }
    @endphp
@else
    {!! $column['text'] !!}
@endif

@includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
