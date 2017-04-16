//Tags AutoComplete parameters    
    function autoComplete(path, list, arrName, type ){
        return ({
            source: function( query, process){
                return $.get(path, { query: query}, function(data){
                   return process(data);
                })
            },
            updater:function (item) {
                //Get the current values of list items in the unordered list 'tagsList'
                var currentItems = list.getElementsByTagName('li');
                var itemName = (item.name) ? item.name : item;
                //check if it's already included
                var notFound = true;
                for(var i = 0; i < currentItems.length; i++){
                    if( currentItems[i].textContent == itemName ) {
                        notFound = false;
                    }
                }
                if(notFound){
                    //Create a new list item li
                    var entry = document.createElement('li');
                    entry.setAttribute("name",arrName);
                    entry.setAttribute("value",itemName);
                    //Add the item name and the delete button
                    if(type == 1)
                        var text = '<button class="organiser-close-icon "></button>';
                    else
                        var text = '<button class="tags-close-icon "></button>';
                    entry.innerHTML = text + itemName;
                    list.appendChild(entry);
                    if(type == 1){
                        applyOrganisers();
                    }

                }
                //Don't return the item name back in order not to keep it in the text box field
                return ;
            }
        });
    }
    function getListInfo(list){
        //Reading list elements
        var currentItems = list.getElementsByTagName('li');
        var elements =[];
        for(var i = 0; i < currentItems.length; i++){
            elements[i] = currentItems[i].textContent;
        }
        return elements;
    }
    function getCurrentFilters(){
        //Reading Tags
        var tags = getListInfo(tagsList);
        //Reading Judges info
        var judges = [];
        var j = 0;
        var checkboxes = document.getElementsByClassName('judgeState');
        for(var i=0; checkboxes[i]; ++i){
            if(checkboxes[i].checked){
                judges[j] = checkboxes[i].value;
                j = j + 1;
            }
        }
        //Then you have now judges and tags
        return({'cTags' : tags,'cJudges': judges });
    }
