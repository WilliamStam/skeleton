const path = require("path");

module.exports = {
    outputDir: "./web",
    assetsDir: "./assets",
    chainWebpack: config => {
        // use the /src/public folder instead of the ./public cause the root public belongs to php
        config.plugins.has("copy") && config.plugin("copy")
            .tap(([args]) => {
                args[0].from = ("./src/public");
                return [args];
            });

        config
            .plugin("html")
            .tap(([args]) => {

                args.template = "./src/public/index.html";
                return [args];
            });
    },
    css: {
        loaderOptions: {
            sass: {
                // make the variables scss file available to all .vue and .scss files. so just use the variable name wherever you need to
                prependData: `@import "./src/assets/css/variables";`
            }
        }
    },
    devServer: {
        // This will forward any request that does not match a static file to this path
        proxy: 'http://skeleton.localhost/'
    }
};

