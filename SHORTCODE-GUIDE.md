# Medium Posts Importer - Shortcode Guide

## 🎉 Version 1.5.0 - Homepage Injection Disabled

Your homepage now shows only WordPress posts. Medium posts are available via shortcodes!

---

## 📋 Quick Start

Add this to any page or post:

```
[medium_posts]
```

This displays your 10 most recent Medium posts in a 3-column grid.

---

## 🎨 All Available Shortcode Parameters

### Basic Parameters

| Parameter | Options | Default | Description |
|-----------|---------|---------|-------------|
| `count` | Number (1-50) | 10 | How many posts to show |
| `columns` | 1, 2, 3, or 4 | 3 | Number of columns (grid/featured layout only) |
| `layout` | grid, list, compact, featured | grid | Display style |
| `order` | asc, desc | desc | Sort order (newest first or oldest first) |

### Display Options

| Parameter | Options | Default | Description |
|-----------|---------|---------|-------------|
| `show_excerpt` | 1 or 0 | 1 | Show/hide post excerpts |
| `show_image` | 1 or 0 | 1 | Show/hide featured images |
| `show_date` | 1 or 0 | 1 | Show/hide publish date |
| `show_categories` | 1 or 0 | 1 | Show/hide categories/tags |
| `excerpt_length` | Number (10-200) | 50 | Number of words in excerpt |

### Advanced Options

| Parameter | Options | Default | Description |
|-----------|---------|---------|-------------|
| `category` | Category name | (none) | Filter by specific category |

---

## 🌟 Layout Examples

### 1. **Grid Layout** (Default)
Cards arranged in columns with images, excerpts, and metadata.

```
[medium_posts]
```

**With custom settings:**
```
[medium_posts count="9" columns="3"]
```

---

### 2. **List Layout**
Horizontal cards with image on the left, perfect for sidebars or narrow spaces.

```
[medium_posts layout="list" count="5"]
```

---

### 3. **Compact Layout**
Minimal design, titles and short excerpts only, no images.

```
[medium_posts layout="compact" count="10"]
```

**Great for:** Sidebars, footers, or "More Articles" sections

---

### 4. **Featured Layout**
First post is large and prominent, others are in a grid below.

```
[medium_posts layout="featured" count="7" columns="3"]
```

**Perfect for:** Homepage sections, landing pages

---

## 💡 Common Use Cases

### Homepage Hero Section
Show your 3 latest posts in a featured layout:
```
[medium_posts layout="featured" count="3" show_excerpt="1"]
```

---

### Full Archive Page
Show all your posts in a list:
```
[medium_posts count="50" layout="list"]
```

---

### Sidebar Widget
Show 5 recent posts, compact style:
```
[medium_posts layout="compact" count="5" show_excerpt="0"]
```

---

### Topic-Specific Page
Show only AI-related posts:
```
[medium_posts category="AI" count="20"]
```

---

### Minimal Title List
Just titles and dates, no images or excerpts:
```
[medium_posts layout="compact" show_excerpt="0" show_image="0" count="15"]
```

---

### Two-Column Layout
```
[medium_posts columns="2" count="6"]
```

---

### Four-Column Grid
```
[medium_posts columns="4" count="12"]
```

---

### Oldest First
Show your earliest posts first:
```
[medium_posts order="asc" count="10"]
```

---

## 🎯 Advanced Examples

### Landing Page Hero
```
[medium_posts layout="featured" count="1" show_categories="0" excerpt_length="100"]
```

---

### Blog Archive with Filters
On your "Medium Articles" page, create multiple sections:

**Latest Posts:**
```
<h2>Latest Articles</h2>
[medium_posts count="3" layout="featured"]
```

**All Posts:**
```
<h2>All Articles</h2>
[medium_posts count="50" layout="list"]
```

---

### Category Pages
Create separate pages for different topics:

**AI Articles Page:**
```
[medium_posts category="AI" count="20"]
```

**Design Articles Page:**
```
[medium_posts category="Design" count="20"]
```

---

### Sidebar "Latest Posts"
```
<h3>Latest from Medium</h3>
[medium_posts layout="compact" count="5" show_excerpt="0" show_categories="0"]
```

---

## 🔧 Pro Tips

1. **Performance**: Start with `count="10"` and increase only if needed. Each post adds load time.

2. **Mobile**: The plugin automatically adjusts to mobile screens (single column on small screens).

3. **Mixing Layouts**: Use different shortcodes on different pages for variety.

4. **Testing**: Add a shortcode to a draft page first to test before publishing.

5. **Categories**: To find your category names, check your Medium posts' tags.

---

## 🚀 Quick Recipes

### Recipe 1: "Magazine Style Homepage"
```
<div style="max-width: 1200px; margin: 0 auto;">
    <h1>Latest Articles</h1>
    [medium_posts layout="featured" count="7" columns="3"]
</div>
```

---

### Recipe 2: "Simple Archive Page"
```
<h1>All My Medium Articles</h1>
<p>A collection of my writing on design, technology, and creativity.</p>
[medium_posts layout="list" count="50"]
```

---

### Recipe 3: "Homepage Highlights"
```
<section style="padding: 3rem 0; background: #f5f5f5;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
        <h2>Featured Articles</h2>
        [medium_posts count="6" columns="3" excerpt_length="30"]
    </div>
</section>
```

---

### Recipe 4: "Sidebar Widget"
```
<div class="widget">
    <h3>From My Medium</h3>
    [medium_posts layout="compact" count="5" show_excerpt="0" show_image="0"]
</div>
```

---

## 📱 Responsive Behavior

All layouts automatically adapt to screen sizes:
- **Desktop**: Shows full column layout
- **Tablet**: Reduces to 2 columns max
- **Mobile**: Single column, full width

---

## 🎨 Customizing Styles

The plugin includes default styling, but you can customize via your theme's CSS:

```css
/* Make titles bigger */
.mpi-post-title {
    font-size: 1.5rem;
}

/* Change link color */
.mpi-read-more {
    color: #your-brand-color;
}

/* Adjust card spacing */
.mpi-posts-grid {
    gap: 3rem;
}
```

---

## ⚙️ Settings Page

Go to **Settings → Medium Posts** to configure:
- Your Medium handle (@jamierothwell)
- Default post count
- Cache duration (12 hours recommended)
- **"Inject into Homepage"** - Keep this UNCHECKED to disable homepage injection

---

## 🆘 Troubleshooting

**Q: Shortcode displays as text instead of posts**
A: Make sure you're using the shortcode in a page/post editor, not in a code/HTML block.

**Q: Images not showing**
A: Try `show_image="1"` explicitly, or check if your Medium posts have images.

**Q: Categories not working**
A: Category names are case-sensitive. Check your Medium post tags for exact spelling.

**Q: Posts not updating**
A: Clear your WordPress cache, or wait for the 12-hour cache to expire.

---

## 📚 Need Help?

Remember: You can combine parameters for unique displays!

```
[medium_posts count="15" layout="list" show_categories="0" excerpt_length="40"]
```

Mix and match to create the perfect display for your site! 🎨
