let userList;
userList.controller = function($scope,userservice){
    $scope.loading = true;
    userservice.getAll(function (e) {
        $scope.loading = false;
        $scope.list = e;
    });
    $scope.remove=function($index,item){
        userservice.remove(item.id, function (e) {
            $scope.list.splice($index, 1);
        });
    } 
}
userList.component = {
       template: `
       <h1  ng-init=" $root.title=trans(user.list|user) ">trans(user.list|user)</h1>
       <div ng-show="loading">
           Loading
       </div>
       <div  ng-hide="loading"> 
           <ul >
               <li ng-repeat="item in list">
                   <a href="#user/"{{item.id}}>{{item.username}}  </a>
                   <div  ng-click="remove($index,item)">X</div>
               </li>
           </ul>
       </div>
    `,
    styles: [``]
}
module.exports = userList;