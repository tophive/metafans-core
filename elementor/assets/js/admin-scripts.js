jQuery( document ).ready( () => {
	const add_btn = document.getElementsByClassName('btn-th-display-condition');
    const remove_btn = document.getElementsByClassName('btn-th-condition-remove');
    const controlls_div = document.getElementsByClassName('controls-inner');
    let conditions = th_elem_locals.header_conditions ? JSON.parse(th_elem_locals.header_conditions) : [];
    let pages = th_elem_locals.all_pages ? JSON.parse(th_elem_locals.all_pages) : [];
    let posts = th_elem_locals.all_posts ? JSON.parse(th_elem_locals.all_posts) : [];
    let condition_rules = th_elem_locals.condition_rules ? JSON.parse(th_elem_locals.condition_rules) : [];

    console.log(th_elem_locals.header_conditions);

    //Pages list
    const add_pages_list = (id) => {
        let pages_list = `<select name="all_pages" class="all_page_list hidden list-${id}" data-conditionid="${id}">`;
        pages_list += '<option value="">Select page</option>';
        pages.forEach(element => {
            pages_list += `<option value="${element['id']}">${element['title']}</option>`
        });
        return pages_list += '</select>';
    }
    const add_posts_list = (id) => {
        // Post list
        let posts_list = `<select name="all_posts" class="all_post_list hidden list-${id}" data-conditionid="${id}">`;
        posts_list += '<option value="">Select post</option>';
    
        posts.forEach(element => {
            posts_list += `<option value="${element['id']}">${element['title']}</option>`
        });
        return posts_list += '</select>';
    }

    // Condition rules

    const add_condition_rule = (id) => {
        let condition_rules_html = `<select class="condition_rules" data-conditionid="${id}">`;
    
        condition_rules.forEach(element => {
            condition_rules_html += `<option value="${element['id']}">${element['name']}</option>`
        });
        condition_rules_html += '</select>';

        return condition_rules_html;
    }

    jQuery(add_btn).on('click', (e) => {
        e.preventDefault();
        let randID = makeid(10);
        add_single_control( randID );
        updateItem(randID, 'in', 'all', '');
    });

    const removeItem = (id) => {
        let i = conditions.findIndex( e => e.id === id );
        if(  i > -1 ){
            conditions.splice( i, 1);
        }

        jQuery('#display_conditions').val(JSON.stringify(conditions));
    }
    
    const updateItem = (id, type, rule, payload = '') => {
        let cndtn = {type,rule,payload};
        let newCondition = {
            id: `th_elem_head_${id}`,
            conditions: cndtn
        }
        let i = conditions.findIndex( e => e.id === `th_elem_head_${id}` );
        if( i > -1 ){
            conditions[i] = newCondition;
        }else{
            conditions.push(newCondition);
        }
        console.log(conditions);
        jQuery('#display_conditions').val(JSON.stringify(conditions));
    }

    jQuery( document ).on('click', remove_btn, (e) => {
        let id = e.target.dataset.id;
        if( id !== 'undefined' ){
            jQuery(document).find('#' + id).remove();
        }
        removeItem(id)
    });

    jQuery( document ).on( 'change', '.condition_types, .condition_rules, .all_page_list, .all_post_list', function(){
        let type = jQuery(this).parents('.single-control').find('.condition_types').val();
        let id = this.dataset.conditionid;
        let rule = jQuery(this).parents('.single-control').find('.condition_rules').val();
        let payload = '';
        if( rule === 'page_id' ){
            payload = jQuery(this).parents('.single-control').find('.all_page_list').val();
            jQuery('.all_page_list.list-' + id).removeClass('hidden');
            jQuery('.all_post_list.list-' + id).addClass('hidden');
        }else if( rule === 'post_id' ){
            payload = jQuery(this).parents('.single-control').find('.all_post_list').val();
            jQuery('.all_page_list.list-' + id).addClass('hidden');
            jQuery('.all_post_list.list-' + id).removeClass('hidden');
        }else{
            jQuery('.all_page_list.list-' + id).addClass('hidden');
            jQuery('.all_post_list.list-' + id).addClass('hidden');
        }
        updateItem(id, type, rule, payload );
    });
    
    function add_single_control(id){
        let single_control = `
            <div class="single-control" id="th_elem_head_${id}">
                <div class="concluder">
                    <select class="condition_types" data-conditionid="${id}">
                        <option value="in">Include</option>
                        <option value="out">Exclude</option>
                    </select>
                </div>
                <div class="dropdown_rules">
                    ${add_condition_rule(id)}
                    ${add_pages_list(id)}
                    ${add_posts_list(id)}
                </div>
                <svg class="btn-th-condition-remove" data-id="th_elem_head_${id}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                </svg>
            </div>
        `;

        jQuery(controlls_div).append(single_control);
    }

    function makeid(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < length) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
          counter += 1;
        }
        return result;
    }
})