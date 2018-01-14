        <section id="editview">
            <div class="header">
                <button class="button border is-green is-primary"><i class="fa fa-save"></i>Save</button>
                <button class="button border"><i class="fa fa-clone"></i>Save as copy</button>
                <button class="button border is-red"><i class="fa fa-trash"></i>Delete</button>
                <label class="button border" for="edit-toggle"><i class="fa fa-ban"></i>Close</label>
            </div>
            <div class="content">
                <label for="input_title">Title</label>
                <input type="text" id="input_title" placeholder="Title of the page, as shown in menu">
                <label for="input_head">Head</label>
                <input type="text" id="input_head" placeholder="Page heading" data-placeholder="input_title">
                <label for="input_subtitle">Subtitle</label>
                <input type="text" id="input_subtitle" placeholder="Subtitle">
                <label for="input_slug">Slug</label>
                <input type="text" id="input_slug" placeholder="Slug" data-placeholder="input_title:str_slug">
                <label for="input_seo_title">SEO Title</label>
                <input type="text" id="input_seo_title" placeholder="Title to use in HTML title, browsers show this in tabs and when added to favorites.">
                <label for="input_body">Body</label>
                <textarea type="text" id="input_body" placeholder="The page content"></textarea>
            </div>
        </section>
