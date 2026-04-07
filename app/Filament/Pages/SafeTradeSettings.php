<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SafeTradeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'SafeTrade & Payments';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'SafeTrade & Payment Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('safetrade');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SafeTrade System')
                    ->description('Configure the SafeTrade secure transaction system')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enable SafeTrade')
                            ->helperText('When disabled, buyers cannot initiate safe transactions'),
                        Forms\Components\TextInput::make('escrow_fee_percent')
                            ->label('Escrow Fee (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(15)
                            ->step(0.1)
                            ->suffix('%')
                            ->helperText('Fee percentage charged for escrow protection'),
                        Forms\Components\TextInput::make('min_transaction_amount')
                            ->label('Minimum Transaction Amount')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('€')
                            ->helperText('Minimum vehicle price for SafeTrade eligibility'),
                        Forms\Components\TextInput::make('max_transaction_amount')
                            ->label('Maximum Transaction Amount')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('€')
                            ->helperText('Maximum transaction amount (0 = no limit)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Transaction Timeframes')
                    ->description('Define deadlines and timeframes for transactions')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\TextInput::make('payment_deadline_hours')
                            ->label('Payment Deadline')
                            ->numeric()
                            ->suffix('hours')
                            ->helperText('Hours buyer has to complete payment after initiating'),
                        Forms\Components\TextInput::make('delivery_deadline_days')
                            ->label('Delivery Deadline')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Days seller has to deliver vehicle after payment'),
                        Forms\Components\TextInput::make('inspection_period_days')
                            ->label('Inspection Period')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Days buyer can inspect before funds are released'),
                        Forms\Components\TextInput::make('auto_release_days')
                            ->label('Auto-Release Period')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Days after delivery before funds auto-release to seller'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dispute Settings')
                    ->description('Configure dispute handling parameters')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->schema([
                        Forms\Components\Toggle::make('disputes_enabled')
                            ->label('Enable Disputes')
                            ->helperText('Allow buyers to open disputes on transactions'),
                        Forms\Components\TextInput::make('dispute_deadline_days')
                            ->label('Dispute Filing Deadline')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Days after delivery to file a dispute'),
                        Forms\Components\TextInput::make('dispute_resolution_days')
                            ->label('Resolution Timeframe')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Target days to resolve a dispute'),
                        Forms\Components\TextInput::make('max_evidence_files')
                            ->label('Max Evidence Files')
                            ->numeric()
                            ->helperText('Maximum files per evidence submission'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Methods')
                    ->description('Configure accepted payment methods')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Toggle::make('bank_transfer_enabled')
                            ->label('Bank Transfer (SEPA)')
                            ->helperText('Accept SEPA bank transfers'),
                        Forms\Components\Toggle::make('paypal_enabled')
                            ->label('PayPal')
                            ->helperText('Accept PayPal payments'),
                        Forms\Components\Toggle::make('stripe_enabled')
                            ->label('Stripe')
                            ->helperText('Accept card payments via Stripe'),
                        Forms\Components\TextInput::make('escrow_bank_name')
                            ->label('Escrow Bank Name')
                            ->helperText('Bank name displayed to buyers for payment'),
                        Forms\Components\TextInput::make('escrow_iban')
                            ->label('Escrow IBAN')
                            ->helperText('IBAN for escrow account'),
                        Forms\Components\TextInput::make('escrow_bic')
                            ->label('Escrow BIC/SWIFT')
                            ->helperText('BIC code for escrow account'),
                        Forms\Components\TextInput::make('escrow_account_holder')
                            ->label('Escrow Account Holder')
                            ->helperText('Account holder name displayed to buyers'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue("safetrade.{$key}", $value);
        }

        Notification::make()
            ->title('SafeTrade settings saved')
            ->success()
            ->send();
    }
}
