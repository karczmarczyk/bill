class collectionFormTable {

    $collectionHolder;

    // setup an "add a tag" link
    $addTagButton = $('<button type="button" class="add_tag_link btn btn-success">Dodaj</button>');
    $newLinkLi = $('<div style="text-align:right"></div>').append(this.$addTagButton);

    constructor(element) {
        var that = this;
        // Get the ul that holds the collection of tags
        that.$collectionHolder = $(element); //'ul.positions'

        // add the "add a tag" anchor and li to the tags ul
        that.$collectionHolder.after(that.$newLinkLi);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        that.$collectionHolder.data('index', that.$collectionHolder.find(':input').length);

        that.$addTagButton.on('click', function (e) {
            // add a new tag form (see next code block)
            that.addTagForm(that.$collectionHolder, that.$newLinkLi);
        });

        // add a delete link to all of the existing tag form li elements
        that.$collectionHolder.find('tr').each(function () {
            that.addTagFormDeleteLink($(this));
        });
    }

    addTagForm($collectionHolder, $newLinkLi) {
        var that = this;
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        var newForm = prototype;
        // You need this only if you didn't set 'label' => false in your tags field in TaskType
        // Replace '__name__label__' in the prototype's HTML to
        // instead be a number based on how many items we have
        // newForm = newForm.replace(/__name__label__/g, index);

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        newForm = newForm.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
         var $newFormLi = $(newForm);
        $collectionHolder.append($newFormLi);
        that.addTagFormDeleteLink($newFormLi);
    }

    addTagFormDeleteLink($tagFormLi) {
        var $removeFormButton = $('<button class="btn btn-danger" type="button">Usuń</button>');
        $tagFormLi.find(".option-column").append($removeFormButton);
        $removeFormButton.on('click', function (e) {
            // remove the li for the tag form
            $tagFormLi.remove();
        });
    }
}