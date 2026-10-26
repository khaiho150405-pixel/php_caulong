<?php

/*
|--------------------------------------------------------------------------
| Thông báo lỗi Validation tiếng Việt
|--------------------------------------------------------------------------
| Được dùng khi APP_LOCALE=vi. Tên trường hiển thị được khai báo trong
| phương thức attributes() của từng Form Request (app/Http/Requests).
*/

return [
    'accepted' => ':Attribute phải được chấp nhận.',
    'after_or_equal' => ':Attribute phải là ngày sau hoặc bằng :date.',
    'alpha_dash' => ':Attribute chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
    'between' => [
        'numeric' => ':Attribute phải nằm trong khoảng :min – :max.',
        'string' => ':Attribute phải có từ :min đến :max ký tự.',
    ],
    'boolean' => ':Attribute phải là đúng hoặc sai.',
    'date' => ':Attribute không phải là ngày hợp lệ.',
    'different' => ':Attribute và :other phải khác nhau.',
    'email' => ':Attribute phải là địa chỉ email hợp lệ.',
    'exists' => ':Attribute đã chọn không hợp lệ.',
    'image' => ':Attribute phải là tệp hình ảnh.',
    'integer' => ':Attribute phải là số nguyên.',
    'max' => [
        'file' => ':Attribute không được lớn hơn :max KB.',
        'numeric' => ':Attribute không được lớn hơn :max.',
        'string' => ':Attribute không được vượt quá :max ký tự.',
    ],
    'mimes' => ':Attribute phải là tệp có định dạng: :values.',
    'min' => [
        'numeric' => ':Attribute phải tối thiểu là :min.',
        'string' => ':Attribute phải có ít nhất :min ký tự.',
    ],
    'numeric' => ':Attribute phải là một số.',
    'prohibited' => 'Không được phép gửi trường :attribute.',
    'regex' => ':Attribute không đúng định dạng.',
    'required' => 'Vui lòng nhập :attribute.',
    'same' => ':Attribute và :other phải giống nhau.',
    'string' => ':Attribute phải là chuỗi ký tự.',
    'unique' => ':Attribute đã tồn tại trong hệ thống.',
    'uploaded' => 'Tải lên :attribute thất bại.',

    'custom' => [],
    'attributes' => [],
];
