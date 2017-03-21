let config;

config.service = {
    mydomain: {
        config: {
            cors: true,
            key: "58af297c-83a8-4838-b5e4-4d3e3ed28ff1",
            api: "https://www.mydomain.com/api/",
            auth: "OAuth2"
        },
        Modules: {
            User: {
                route: "Modules/user/Services/route.js",
                controller: "Modules/user/Services/controller.js"
            }
        }
    }
}

config.component = {
    user_list: {
        url: "/app/user/add",
        view: "Modules/user/View/list/list.js"
    }
}


module.exports = config;