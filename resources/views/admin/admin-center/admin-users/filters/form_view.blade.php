<div class="row">
    <div class="col-md-6">
        <form action="{{ route($options['parent_route'], [$view == 'edit' ? 'update' : 'store', $id]) }}" method="POST" enctype="multipart/form-data">
            @foreach ($options['columns'][$view] as $field)
                @if(is_array($field))
                    @continue
                @endif
                @php
                    $fieldValue = isset($data->$field) ? $data->$field : null;
                @endphp
                {!! $form->generateFormField([
                    'name' => $field,
                    'type' => $form->getFormType($field),
                    'label' => $form->formatTitle($field),
                    'attributes' => [],
                    'value' => $fieldValue,
                ], $options, $fieldValue) !!}
            @endforeach
            <button type="submit" class="btn btn-primary float-right mt-2">Submit</button>
        </form>
    </div>
    <div class="col-md-6">
        <div class="h-100 d-flex flex-column bg-info justify-content-center align-items-center">
            @if ($view == 'edit')
                <i class="fas fa-edit fa-3x mb-3"></i>
                <h4>You are editing</h4>
            @else
                <i class="fas fa-plus fa-3x mb-3"></i>
                <h4>Create a new Record</h4>
            @endif
            <p class="text-muted"></p>
        </div>
    </div>
</div>
