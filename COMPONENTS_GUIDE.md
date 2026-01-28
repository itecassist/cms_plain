# Component System - Quick Guide

## What Are Components?

Components are reusable HTML or PHP snippets that you can include anywhere in your content using a simple placeholder syntax.

## Syntax

Use double curly braces to include a component:
```
{{component-name}}
```

## Example Usage

In your page content editor, type:
```html
<div class="footer-social">
    {{social-links}}
</div>

<div class="contact-section">
    {{contact-form}}
</div>
```

## Creating Components

1. Create a new file in `components/` directory
2. Use `.html` for static HTML or `.php` for dynamic PHP components
3. Name it descriptively: `social-links.html`, `contact-form.php`, etc.
4. Use `{{filename-without-extension}}` in your content

## Component File Location

```
components/
  social-links.html
  contact-form.php
  newsletter-signup.html
  footer-cta.php
```

## HTML Components (Static)

**File:** `components/social-links.html`
```html
<ul class="social-list">
    <li><a target="_blank" href="https://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
    <li><a target="_blank" href="https://twitter.com"><i class="fa fa-twitter"></i></a></li>
    <li><a target="_blank" href="https://www.instagram.com"><i class="fa fa-instagram"></i></a></li>
</ul>
```

## PHP Components (Dynamic)

**File:** `components/contact-form.php`
```php
<?php
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form data
}
?>

<form method="POST">
    <input type="text" name="name" required>
    <button type="submit">Submit</button>
</form>
```

**Features of PHP Components:**
- ✅ Full PHP execution
- ✅ Form processing
- ✅ Database queries
- ✅ Session access
- ✅ Include JavaScript
- ✅ Dynamic content generation

## Usage in Content

```html
<section class="contact">
    <h2>Get In Touch</h2>
    {{contact-form}}
</section>

<footer>
    {{social-links}}
</footer>
```

## Benefits

✅ **Reusable** - Write once, use everywhere
✅ **Dynamic** - PHP components can process forms, access database
✅ **Consistent** - Same code across all pages
✅ **Easy Updates** - Change one file, updates all pages
✅ **Clean** - Keep main content focused on structure
✅ **JavaScript Support** - Include JS in any component

## Valid Component Names

- Use letters, numbers, hyphens, and underscores
- Examples: `social-links`, `contact-form`, `newsletter`, `cta-banner`
- Case sensitive

## How It Works

1. You type `{{contact-form}}` in the editor
2. Content is saved to `content/page.json`
3. When page loads, system checks for `components/contact-form.php` (then `.html`)
4. PHP components are executed; HTML components are loaded
5. Output replaces `{{contact-form}}`
6. Visitor sees the fully rendered result

## Tips

- Use `.php` for forms, dynamic content, database queries
- Use `.html` for static elements like social links, footers
- Keep components simple and focused
- Test components after creating them
- PHP components have access to all functions and variables
- JavaScript works in both HTML and PHP components
