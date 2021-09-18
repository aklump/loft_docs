# Token Replacement

> @todo This needs to be rewritten due to the [new linking feature](@linking).

> See also [frontmatter](@frontmatter) for updated info.
---

Using frontmatter, you can define tokens in your markdown file that will be replaced during render.  See examples below.


## Example 1: Tokens

List your find/replace tokens in frontmatter under the key `tokens`:

Here is your markdown document:

    ---
    tokens:
        @modified: October 15, 2018
        @s1: what-info-do-we-collect
    ---
    # PRIVACY NOTICE
    
    Last Modified @modified
    
    * [What Information Do We Collect?](#@s1)
    
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.
    
    <a name="@ld--tokens"></a>
    ## WHAT INFORMATION DO WE COLLECT?
    
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.
    
    This document was last updated on {{ modified }}.

## Example 2: Twig Style

See how you can use twig-style tokens by replacing the frontmatter `tokens` with `twig`.  Notice that the find keys do not include the `{{` and `}}`, however in the body of the document they are present.

Here is your markdown document:

    ---
    twig:
        modified: October 15, 2018
        l1: what-info-do-we-collect
    ---
    # PRIVACY NOTICE
    
    Last Modified {{ modified }}
    
    * [What Information Do We Collect?](#{{ s1 }})
    
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.
    
    <a name="{{ s1 }}"></a>
    ## WHAT INFORMATION DO WE COLLECT?
    
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.
    
    This document was last updated on {{ modified }}.
