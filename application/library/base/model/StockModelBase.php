<?php
namespace base\model;


class StockModelBase {
    protected $stock_id
    ,$stock_price
    ,$open
    ,$close
    ,$lup
    ,$ldown
    ,$highest
    ,$lowest
    ,$average
    ,$change
    ,$volume
    ,$amount
    ,$profit
    ,$amplitude
    ,$inside
    ,$outside
    ,$weibiRatio
    ,$volumeRatio
    ,$bvRatio
    ,$lyr
    ,$peratio
    ,$circulatingCapital
    ,$perShareEarn
    ,$netAssetsPerShare
    ,$totalShareCapital
    ,$priceLimit;

    public function __construct($stock_id, $data)
    {
        
        $this->stock_id = $stock_id;

        $this->stock_price = $data['stock_price'] ?? 0.00;
        $this->open = $data['open'] ?? 0.00;
        $this->close = $data['close'] ?? 0.00;
        $this->lup = $data['lup'] ?? 0.00;
        $this->ldown = $data['ldown'] ?? 0.00;
        $this->highest = $data['highest'] ?? 0.00;
        $this->lowest = $data['lowest'] ?? 0.00;
        $this->average = $data['average'] ?? 0.00;
        $this->amplitude = $data['amplitude'] ?? 0.00;
        $this->volume = $data['volume'] ?? 0;
        $this->amount = $data['amount'] ?? 0.00;
        $this->change = $data['change'] ?? 0.00;
        $this->profit = $data['profit'] ?? 0.00;
        $this->inside = $data['inside'] ?? 0.00;
        $this->outside = $data['outside'] ?? 0.00;
        $this->weibiRatio = $data['weibiRatio'] ?? 0.00;
        $this->volumeRatio = $data['volumeRatio'] ?? 0.00;
        $this->bvRatio = $data['bvRatio'] ?? 0.00;
        $this->lyr = $data['lyr'] ?? 0.00;
        $this->peratio = $data['peratio'] ?? 0.00;
        $this->circulatingCapital = $data['circulatingCapital'] ?? 0.00;
        $this->perShareEarn = $data['perShareEarn'] ?? 0.00;
        $this->netAssetsPerShare = $data['netAssetsPerShare'] ?? 0.00;
        $this->totalShareCapital = $data['totalShareCapital'] ?? 0.00;
        $this->priceLimit = $data['priceLimit'] ?? 0.00;
    }

}
