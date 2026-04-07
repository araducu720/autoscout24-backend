<?php

return [
    'payment_uploaded' => [
        'subject' => 'Comprovativo de pagamento recebido - Transação :reference',
        'message' => 'O comprovativo de pagamento foi enviado e aguarda verificação',
        'admin' => [
            'subject' => 'Verificação de pagamento necessária - :reference',
            'message' => 'Um novo comprovativo de pagamento requer verificação',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Pagamento verificado - Os seus fundos estão seguros!',
            'message' => 'O pagamento da transação :reference foi verificado',
            'intro' => 'Ótimas notícias! O pagamento foi verificado pela nossa equipa.',
            'next_steps' => 'Pode agora agendar a entrega do veículo com o concessionário.',
        ],
        'dealer' => [
            'subject' => 'Pagamento verificado - Veículo pronto para levantamento',
            'message' => 'O seu pagamento da transação :reference foi verificado',
            'intro' => 'O seu pagamento foi verificado com sucesso.',
            'next_steps' => 'Por favor, agende o levantamento do veículo com o vendedor.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Levantamento agendado para :date',
        'message' => 'O levantamento do veículo foi agendado',
        'seller' => [
            'intro' => 'O concessionário agendou o levantamento do veículo.',
        ],
        'dealer' => [
            'intro' => 'O levantamento foi confirmado.',
        ],
    ],
    'pickup_proposed_subject' => 'Datas de levantamento propostas para :vehicle',
    'pickup_proposed_message' => 'Novas datas de levantamento foram propostas. Por favor, reveja e confirme.',
    'pickup_confirmed_subject' => 'Levantamento confirmado para :vehicle',
    'pickup_confirmed_message' => 'Data de levantamento confirmada para :date',
    'handover_confirmed' => [
        'subject' => 'Entrega do veículo confirmada',
        'message' => 'A entrega do veículo foi confirmada',
        'intro' => 'O veículo foi entregue com sucesso.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transação concluída - :reference',
            'message' => 'A venda do seu veículo foi concluída com sucesso!',
            'greeting' => 'Parabéns!',
            'intro' => 'A venda do seu veículo foi concluída. Os fundos serão transferidos para a sua conta bancária.',
            'thanks' => 'Obrigado por utilizar o AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transação concluída - :reference',
            'message' => 'A compra do seu veículo foi concluída com sucesso!',
            'greeting' => 'Parabéns!',
            'intro' => 'A compra do seu veículo foi concluída.',
            'thanks' => 'Obrigado por utilizar o AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transação cancelada - :reference',
        'message' => 'A transação :reference foi cancelada',
        'intro' => 'Infelizmente, esta transação foi cancelada.',
        'reason' => 'Motivo: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Disputa aberta - Transação :reference',
        'message' => 'Uma disputa foi aberta para a transação :reference',
        'intro' => 'Uma disputa foi apresentada para esta transação.',
        'admin' => [
            'subject' => 'Nova disputa requer atenção - :reference',
            'message' => 'Uma nova disputa foi aberta e requer análise',
        ],
    ],
    'common' => [
        'vehicle' => 'Veículo',
        'amount' => 'Montante',
        'reference' => 'Referência',
        'date' => 'Data',
        'status' => 'Estado',
        'view_details' => 'Ver detalhes',
        'contact_support' => 'Contactar suporte',
        'questions' => 'Se tiver alguma questão, por favor contacte a nossa equipa de suporte.',
    ],
];
