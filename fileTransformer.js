// fileTransformer.js
const path = require('path');

module.exports = {
    process(src, filename, config, options) {
        return 'module.exports = ' + JSON.stringify({
            resolve: {
                alias: {
                    vue: './node_modules/vue',
                }
            }
        }) + ';';
    },

};
