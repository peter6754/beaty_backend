<div class="coupon_order" style="background-color: <?= $coupon->category->color ?>">
    <div class="row">
        <div class="col-md-4 col-4">
            <img src="<?= $coupon->getImageUrl("250x250") ?>" />
        </div>
        <div class="col-md-8 col-8">
            <div class="coupon_description">
                <div class="coupon_left">
                    <span class="coupon_type">КУПОН</span>
                    <h4><?= $coupon->name ?></h4>
                </div>
                <div class="coupon_amount">-<?= $coupon->amount ?>р.</div>
                <span class="coupon_name"><?= $coupon->description ?></span>
                <div class="coupon_bottom">
                    <span class="coupon_price"><?= $coupon->price ?>р.</span>
                    <div class="buttons">
                        <button id="add_coupon" data-coupon_id="<?= $coupon->id ?>"
                            data-price="<?= ($product->price - $coupon->amount) ?>р."
                            class="btn btn-sm btn-primary">Добавить купон</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="total_order">
    <div class="row">
        <div class="col-md-6 col-6">
            <span>Итого:</span>
        </div>
        <div class="col-md-6 col-6 text-right">
            <span id="total_price"><?= $product->price ?>р.</span>
        </div>
    </div>
</div>