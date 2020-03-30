<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute phải được chấp nhận.',
    'active_url'           => ':attribute không phải là một URL hợp lệ.',
    'after'                => ':attribute phải là một ngày sau ngày :date.',
    'after_or_equal'       => ':attribute phải là thời gian bắt đầu sau hoặc đúng bằng :date.',
    'alpha'                => ':attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash'           => ':attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.',
    'alpha_num'            => ':attribute chỉ có thể chứa chữ cái và số.',
    'array'                => ':attribute phải là dạng mảng.',
    'before'               => ':attribute phải là một ngày trước ngày :date.',
    'before_or_equal'      => ':attribute phải là thời gian bắt đầu trước hoặc đúng bằng :date.',
    'between'              => [
        'numeric' => ':attribute phải nằm trong khoảng :min - :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải từ :min - :max kB.',
        'string'  => ':attribute phải từ :min - :max ký tự.',
        'array'   => ':attribute phải có từ :min - :max phần tử.',
    ],
    'boolean'              => ':attribute phải là true hoặc false.',
    'confirmed'            => 'Giá trị xác nhận trong trường :attribute không khớp.',
    'date'                 => ':attribute không phải là định dạng của ngày-tháng.',
    'date_equals'          => ':attribute phải là một ngày bằng với :date.',
    'date_format'          => ':attribute không giống với định dạng :format.',
    'different'            => ':attribute và :other phải khác nhau.',
    'digits'               => 'Độ dài của trường :attribute phải gồm :digits chữ số.',
    'digits_between'       => 'Độ dài của trường :attribute phải nằm trong khoảng :min and :max chữ số.',
    'dimensions'           => ':attribute có kích thước không hợp lệ.',
    'distinct'             => ':attribute có giá trị trùng lặp.',
    'email'                => ':attribute phải là một địa chỉ email hợp lệ.',
    'exists'               => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'file'                 => ':attribute phải là một tệp tin.',
    'filled'               => ':attribute không được bỏ trống.',
    'ends_with' => 'The :attribute must end with one of the following: :values',
    
    'gt'                   => [
        'numeric' => 'Giá trị trường :attribute phải lớn hơn :value.',
        'file'    => 'Dung lượng trường :attribute phải lớn hơn :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải nhiều hơn :value kí tự.',
        'array'   => 'Mảng :attribute phải có nhiều hơn :value phần tử.',
    ],
    'gte'                  => [
        'numeric' => 'Giá trị trường :attribute phải lớn hơn hoặc bằng :value.',
        'file'    => 'Dung lượng trường :attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải lớn hơn hoặc bằng :value kí tự.',
        'array'   => 'Mảng :attribute phải có ít nhất :value phần tử.',
    ],
    'image'                => ':attribute phải là định dạng hình ảnh.',
    'in'                   => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'in_array'             => ':attribute phải thuộc tập cho phép: :other.',
    'integer'              => ':attribute phải là một số nguyên.',
    'ip'                   => ':attribute phải là một địa chỉ IP.',
    'ipv4'                 => ':attribute phải là một địa chỉ IPv4.',
    'ipv6'                 => ':attribute phải là một địa chỉ IPv6.',
    'json'                 => ':attribute phải là một chuỗi JSON.',
    'lt'                   => [
        'numeric' => 'Giá trị trường :attribute phải nhỏ hơn :value.',
        'file'    => 'Dung lượng trường :attribute phải nhỏ hơn :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải nhỏ hơn :value kí tự.',
        'array'   => 'Mảng :attribute phải có ít hơn :value phần tử.',
    ],
    'lte'                  => [
        'numeric' => 'Giá trị trường :attribute phải nhỏ hơn hoặc bằng :value.',
        'file'    => 'Dung lượng trường :attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string'  => 'Độ dài trường :attribute phải nhỏ hơn hoặc bằng :value kí tự.',
        'array'   => 'Mảng :attribute không được có nhiều hơn :value phần tử.',
    ],
    'max'                  => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute không được lớn hơn :max kB.',
        'string'  => ':attribute không được lớn hơn :max ký tự.',
        'array'   => ':attribute không được lớn hơn :max phần tử.',
    ],
    'mimes'                => ':attribute phải là một tập tin có định dạng: :values.',
    'mimetypes'            => ':attribute phải là một tập tin có định dạng: :values.',
    'min'                  => [
        'numeric' => ':attribute phải tối thiểu là :min.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải tối thiểu :min kB.',
        'string'  => ':attribute phải có tối thiểu :min ký tự.',
        'array'   => ':attribute phải có tối thiểu :min phần tử.',
    ],
    'not_in'               => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'not_regex'            => ':attribute có định dạng không hợp lệ.',
    'numeric'              => ':attribute phải là một số.',
    'present'              => ':attribute phải được cung cấp.',
    'regex'                => ':attribute có định dạng không hợp lệ.',
    'required'             => ':attribute không được bỏ trống.',
    'required_if'          => ':attribute không được bỏ trống khi trường :other là :value.',
    'required_unless'      => ':attribute không được bỏ trống trừ khi :other là :values.',
    'required_with'        => ':attribute không được bỏ trống khi một trong :values có giá trị.',
    'required_with_all'    => ':attribute không được bỏ trống khi tất cả :values có giá trị.',
    'required_without'     => ':attribute không được bỏ trống khi một trong :values không có giá trị.',
    'required_without_all' => ':attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same'                 => ':attribute và :other phải giống nhau.',
    'size'                 => [
        'numeric' => ':attribute phải bằng :size.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải bằng :size kB.',
        'string'  => ':attribute phải chứa :size ký tự.',
        'array'   => ':attribute phải chứa :size phần tử.',
    ],
    'starts_with'          => ':attribute phải được bắt đầu bằng một trong những giá trị sau: :values',
    'string'               => ':attribute phải là một chuỗi ký tự.',
    'timezone'             => ':attribute phải là một múi giờ hợp lệ.',
    'unique'               => ':attribute đã có trong cơ sở dữ liệu.',
    'uploaded'             => ':attribute tải lên thất bại.',
    'url'                  => ':attribute không giống với định dạng một URL.',
    'uuid'                 => ':attribute phải là một chuỗi UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
