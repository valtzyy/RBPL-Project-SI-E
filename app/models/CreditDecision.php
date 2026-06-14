<?php

require_once ROOT_PATH . '/core/Model.php';

// Model untuk tabel credit_decisions (PBI-8.6/8.7 akan pakai untuk simpan keputusan)
class CreditDecision extends Model
{
    protected string $table = 'credit_decisions';
}
