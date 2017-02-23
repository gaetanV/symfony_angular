module.exports = {
    getUser : {        
        route: "/user/:id",
        requirements: { id: "\d+" },
        persistence : ["User"],
        role : ["ROLE_USER"],
        method : ["GET"],
        lifetime : "455646",
        sync : true,
    },
}