# Integration with GitHub

GitHub allows for documentation loaded from _docs_ in your repo root.  Here's a strategy for using loft_docs with a github repository.

## File Structure

        .
        ├── docs
        │   ├── how-to.html
        │   └── index.html
        └── documentation
            ├── core
            ├── core-config.sh
            └── source
                └── how-to.md
                
1. Install Loft Docs in your repo in _documentation/core_.
1. Setup _core-config.sh_ to compile `website` to _docs_.

        website_dir = '../docs'
        
1. Ensure that _docs/index.html_ is created on compile.

## Setup on github.com

1. Load your repository page in the browser.
1. Click the _Settings_ link.
1. Scroll down to _GitHub Pages_.
1. Set source to _master branch /docs folder_ and click _Save_.
1. Copy the publish url, e.g. https://aklump.github.io/visual_sitemap/
1. Return to the repo page.
1. Click the edit button.
1. Paste the copied URL into _Website_ and click _Save_.
1. Visit the link and ensure the docs appear as expected.
1. Add something like the following to your README.md file, near the top.

        **Visit <https://aklump.github.io/visual_sitemap> for full documentation.**
