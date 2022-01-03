/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'js/main'
    ],
    map: {
        '*': {
            'Magento_Swatches/js/swatch-renderer': 'Leitner_LeitnerShop/js/swatch-renderer',
            'configurable': 'Leitner_LeitnerShop/js/configurable'
        }
    },
    config: {
        mixins: {
        }
    }
};
