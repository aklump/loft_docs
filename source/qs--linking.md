---
id: linking
---
# Linking to Other Pages

Because filenames may change, as you move files around to different chapters, or simply re-organize, you do not want to write your internal links to other pages based on filename.  Instead you will use the convention descriped here to link from page A to page B, or to a subsection of page B.

## Link to Another Page Top

Study these page snippets below to see how cross-page linking is accomplished.  Notice the following points:

1. The `id` of the page is declared in it's frontmatter, this is the constant that will be used in your links.  This should never change once established.  This is what allows the filenames to be fluid and not break your internal linking.
1. In the link path you will reference your target by it's id, e.g. `@link_from_demo`, `@link_to_demo`, etc.  Notice the `@` symbol as id prefix.

page one: _ex--alpha.md_

    ---
    id: link_from_demo
    ---
    # The First Page
    
    Do you want to [view the next page](@link_to_demo)
    
page two: _ex--branvo.md_

    ---
    id: link_to_demo
    ---
    # The Second Page
    
    Go back to [the first page](@link_from_demo)    

## Link to Another Page Section

This is how you link to a section header on another page.

1. The header id must not contain whitespace, e.g., `extra`

page one: _ex--alpha.md_

    ---
    id: link_from_demo
    ---
    # The First Page
    
    Do you want to [read some extra info?](@link_to_demo:extra)
    
page two: _ex--bravo.md_

    ---
    id: link_to_demo
    ---
    # The Second Page
    
    Go back to [the first page](@link_from_demo)
    
    ##:extra Some Extra Information
    
### Using RAW Html

If you are going to write plain HTML, you write your headers using an `id` attribute like vanilla HTML, but your link tag `href` must use the same format as per Markdown.

page one: _ex--alpha.md_

    ---
    id: link_from_demo
    ---
    # The First Page
    
    Do you want to  <a href="@link_to_demo:extra">read some extra info?</a>
    
page two: _ex--branvo.md_

    ---
    id: link_to_demo
    ---
    # The Second Page
    
    Go back to <a href="@link_from_demo">the first page</a>
    
    <h2 id="extra">Some Extra Information
