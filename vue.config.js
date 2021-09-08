const path = require("path");

module.exports = {
    outputDir: "./web",
    assetsDir: "./assets", // publicPath: 'http://skeleton.localhost/',
    // publicPath: (window.webpackHotUpdate || (
    //     process.env.NODE_ENV !== "production" &&
    //     process.env.NODE_ENV !== "test" &&
    //     typeof console !== "undefined"
    //   )) ? '/' : 'http://skeleton.localhost/',
    chainWebpack: config => {
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
                prependData: `@import "./src/assets/css/variables";`
            }
        }
    },
    devServer: {
        // This will forward any request that does not match a static file to localhost:3000
        proxy: 'http://skeleton.localhost/'
    }
};

