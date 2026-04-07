<?php
// Bulgarian
return [
    'title' => 'Безопасна търговия', 'subtitle' => 'Сигурна търговия с превозни средства',
    'listing' => ['title' => 'Обява за замяна', 'create' => 'Създаване на обява', 'edit' => 'Редактиране на обява', 'publish' => 'Публикуване на обява', 'deactivate' => 'Деактивиране на обява',
        'status' => ['draft' => 'Чернова', 'active' => 'Активна', 'inactive' => 'Неактивна', 'sold' => 'Продадена', 'expired' => 'Изтекла'],
        'fields' => ['make' => 'Марка', 'model' => 'Модел', 'year' => 'Година', 'mileage' => 'Пробег', 'fuel_type' => 'Вид гориво', 'transmission' => 'Скоростна кутия', 'vin' => 'VIN', 'registration' => 'Регистрационен номер', 'exterior_color' => 'Външен цвят', 'interior_color' => 'Цвят на интериора', 'condition' => 'Състояние', 'description' => 'Описание', 'min_price' => 'Минимална приемлива цена', 'max_price' => 'Максимална очаквана цена', 'location' => 'Местоположение'],
        'condition' => ['excellent' => 'Отлично', 'good' => 'Добро', 'fair' => 'Задоволително', 'poor' => 'Лошо']],
        'fields' => ['amount' => 'Сума на офертата', 'message' => 'Съобщение до продавача', 'expires_at' => 'Валидна до']],
    'transaction' => ['title' => 'Транзакция', 'reference' => 'Референция',
        'status' => ['pending_payment' => 'Очаква плащане', 'payment_uploaded' => 'Плащането е качено', 'payment_verified' => 'Плащането е потвърдено', 'vehicle_handed_over' => 'Превозното средство е предадено', 'completed' => 'Завършена', 'cancelled' => 'Отменена', 'disputed' => 'Оспорвана'],
        'actions' => ['upload_payment' => 'Качване на доказателство за плащане', 'confirm_handover' => 'Потвърждаване на предаването', 'cancel' => 'Отмяна на транзакцията', 'open_dispute' => 'Откриване на спор'],
        'payment' => ['due_date' => 'Краен срок за плащане', 'bank_transfer' => 'Банков превод', 'leasing' => 'Лизинг'],
        'bank_details' => ['title' => 'Банкови данни', 'bank_name' => 'Име на банката', 'iban' => 'IBAN', 'bic' => 'BIC/SWIFT', 'account_holder' => 'Титуляр на сметката']],
    'pickup' => ['title' => 'Вземане на превозното средство', 'propose_dates' => 'Предложете дати за вземане', 'confirm_date' => 'Потвърдете дата за вземане', 'reschedule' => 'Пренасрочване на вземането',
        'status' => ['pending' => 'Изчакваща', 'dates_proposed' => 'Предложени дати', 'confirmed' => 'Потвърдена', 'rescheduled' => 'Пренасрочена', 'completed' => 'Завършена'],
        'fields' => ['address' => 'Адрес за вземане', 'phone' => 'Телефон за контакт', 'date' => 'Дата на вземане', 'notes' => 'Бележки']],
    'dispute' => ['title' => 'Спор', 'open' => 'Откриване на спор', 'reference' => 'Референция на спора',
        'status' => ['open' => 'Открит', 'under_review' => 'В процес на преглед', 'awaiting_info' => 'Очаква информация', 'escalated' => 'Ескалиран', 'resolved' => 'Разрешен', 'closed' => 'Затворен'],
        'types' => ['payment_not_received' => 'Плащането не е получено', 'vehicle_not_as_described' => 'Превозното средство не отговаря на описанието', 'delivery_issue' => 'Проблем с доставката', 'documentation_problem' => 'Проблем с документацията', 'fraud' => 'Подозирана измама', 'other' => 'Друго'],
        'priority' => ['low' => 'Нисък', 'medium' => 'Среден', 'high' => 'Висок', 'urgent' => 'Спешен'],
        'resolution' => ['in_favor_seller' => 'Решен в полза на продавача', 'in_favor_dealer' => 'Решен в полза на дилъра', 'mutual_agreement' => 'Взаимно споразумение', 'cancelled' => 'Отменен']],
    'notifications' => [
    'emails' => ['subject' => [
    'errors' => ['not_authorized' => 'Нямате право да извършите това действие', 'invalid_status' => 'Това действие не е налично в текущия статус', 'payment_already_uploaded' => 'Доказателството за плащане вече е качено',
];
