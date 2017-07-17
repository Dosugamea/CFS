$.fn.RangeSlider = function(cfg){
    this.sliderCfg = {
        min: cfg && !isNaN(parseFloat(cfg.min)) ? Number(cfg.min) : null, 
        max: cfg && !isNaN(parseFloat(cfg.max)) ? Number(cfg.max) : null,
        step: cfg && Number(cfg.step) ? cfg.step : 1,
        callback: cfg && cfg.callback ? cfg.callback : null
    };

    var $input = $(this);
    var min = this.sliderCfg.min;
    var max = this.sliderCfg.max;
    var step = this.sliderCfg.step;
    var callback = this.sliderCfg.callback;

    $input.attr('min', -8)
        .attr('max', 8)
        .attr('step', 1);//-8% - +8%

    $input.bind("input", function(e){
        $input.attr('value', this.value);
        $input.css( 'background', 'linear-gradient(to right, #FF6799, #665454 ' + this.value + '%, #665454)' );

        if ($.isFunction(callback)) {
            callback(this);
        }
    });
};