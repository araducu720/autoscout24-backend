<?php

return [
    'payment_uploaded' => [
        'subject' => 'Ödeme kanıtı alındı - İşlem :reference',
        'message' => 'Ödeme kanıtı yüklendi ve doğrulama bekliyor',
        'admin' => [
            'subject' => 'Ödeme doğrulaması gerekli - :reference',
            'message' => 'Yeni bir ödeme kanıtı doğrulama gerektiriyor',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Ödeme doğrulandı - Paranız güvende!',
            'message' => ':reference işlemi için ödeme doğrulandı',
            'intro' => 'Harika haber! Ödeme ekibimiz tarafından doğrulandı.',
            'next_steps' => 'Artık bayi ile araç teslimini planlayabilirsiniz.',
        ],
        'dealer' => [
            'subject' => 'Ödeme doğrulandı - Araç teslimata hazır',
            'message' => ':reference işlemi için ödemeniz doğrulandı',
            'intro' => 'Ödemeniz başarıyla doğrulandı.',
            'next_steps' => 'Lütfen satıcı ile araç teslim alımını planlayın.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => ':date için teslim alma planlandı',
        'message' => 'Araç teslim alma planlandı',
        'seller' => [
            'intro' => 'Bayi araç teslim alımını planladı.',
        ],
        'dealer' => [
            'intro' => 'Teslim alma onaylandı.',
        ],
    ],
    'pickup_proposed_subject' => ':vehicle için teslim alma tarihleri önerildi',
    'pickup_proposed_message' => 'Yeni teslim alma tarihleri önerildi. Lütfen inceleyin ve onaylayın.',
    'pickup_confirmed_subject' => ':vehicle için teslim alma onaylandı',
    'pickup_confirmed_message' => ':date için teslim alma tarihi onaylandı',
    'handover_confirmed' => [
        'subject' => 'Araç teslimi onaylandı',
        'message' => 'Araç teslimi onaylandı',
        'intro' => 'Araç başarıyla teslim edildi.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'İşlem tamamlandı - :reference',
            'message' => 'Araç satışınız başarıyla tamamlandı!',
            'greeting' => 'Tebrikler!',
            'intro' => 'Araç satışınız tamamlandı. Tutarlar banka hesabınıza aktarılacaktır.',
            'thanks' => 'AutoScout24 SafeTrade\'i kullandığınız için teşekkür ederiz!',
        ],
        'dealer' => [
            'subject' => 'İşlem tamamlandı - :reference',
            'message' => 'Araç satın alımınız başarıyla tamamlandı!',
            'greeting' => 'Tebrikler!',
            'intro' => 'Araç satın alımınız tamamlandı.',
            'thanks' => 'AutoScout24 SafeTrade\'i kullandığınız için teşekkür ederiz!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'İşlem iptal edildi - :reference',
        'message' => ':reference işlemi iptal edildi',
        'intro' => 'Maalesef bu işlem iptal edildi.',
        'reason' => 'Sebep: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'İtiraz açıldı - İşlem :reference',
        'message' => ':reference işlemi için bir itiraz açıldı',
        'intro' => 'Bu işlem için bir itiraz dosyalandı.',
        'admin' => [
            'subject' => 'Yeni itiraz ilgi gerektiriyor - :reference',
            'message' => 'Yeni bir itiraz açıldı ve inceleme gerektiriyor',
        ],
    ],
    'common' => [
        'vehicle' => 'Araç',
        'amount' => 'Tutar',
        'reference' => 'Referans',
        'date' => 'Tarih',
        'status' => 'Durum',
        'view_details' => 'Detayları Görüntüle',
        'contact_support' => 'Destek ile İletişime Geçin',
        'questions' => 'Herhangi bir sorunuz varsa, lütfen destek ekibimizle iletişime geçin.',
    ],
];
