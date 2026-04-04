<?php

declare(strict_types=1);

$config = require __DIR__ . '/includes/config.php';
$page_title = $config['site_name'] . ' · Demo landing';
$page_description = 'Single-page demo for the universal blog shell: Home, About, Services, Testimonials, Contact, and Blog.';
require __DIR__ . '/includes/portal-open.php';
?>
        <section id="home" class="ubs-hero">
            <div class="container ubs-hero-inner">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="ubs-kicker"><i class="fa-solid fa-layer-group"></i> Portable blog panel</div>
                        <h1 class="ubs-display-title">Content that travels with your brand — not against your CSS.</h1>
                        <p class="ubs-lead">This page is a casual test front. The same header, footer, and <code class="ubs-code" style="background: rgba(15,23,42,0.08); color: var(--ubs-surface); padding: 0.15rem 0.45rem; border-radius: 6px;">.ubs</code> wrapper power the blog listing and article views so you can drop the folder into another site and tune only <code class="ubs-code" style="background: rgba(15,23,42,0.08); color: var(--ubs-surface); padding: 0.15rem 0.45rem; border-radius: 6px;">includes/config.php</code>.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn ubs-btn" href="<?= htmlspecialchars(bms_url('blog'), ENT_QUOTES, 'UTF-8') ?>">Open blog</a>
                            <a class="btn ubs-btn ubs-btn--ghost" href="#contact">Get in touch</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ubs-hero-card">
                            <div class="row g-3 text-center">
                                <div class="col-4">
                                    <div class="ubs-stat">100%</div>
                                    <div class="ubs-stat-label">Scoped UI</div>
                                </div>
                                <div class="col-4">
                                    <div class="ubs-stat">1</div>
                                    <div class="ubs-stat-label">Admin panel</div>
                                </div>
                                <div class="col-4">
                                    <div class="ubs-stat">∞</div>
                                    <div class="ubs-stat-label">Hosts</div>
                                </div>
                            </div>
                            <hr class="my-4 opacity-25">
                            <p class="mb-0 text-secondary small">Tip: point your main site menu items here — <strong>Home</strong> through <strong>Contact</strong> are in-page anchors; <strong>Blog</strong> loads dynamic posts from MySQL.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="ubs-section ubs-section--alt">
            <div class="container">
                <h2 class="ubs-section-title">About this shell</h2>
                <p class="ubs-section-desc">We keep public chrome inside a single wrapper class so your host page can use its own typography, grid, and components without fighting the blog panel.</p>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="ubs-feature">
                            <div class="ubs-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <h3>Isolation</h3>
                            <p>Layout and components are namespaced with <code class="ubs-code" style="background: #f1f5f9; color: var(--ubs-surface);">ubs-*</code> prefixes inside <code class="ubs-code" style="background: #f1f5f9; color: var(--ubs-surface);">.ubs</code>.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ubs-feature">
                            <div class="ubs-icon"><i class="fa-solid fa-sliders"></i></div>
                            <h3>Config-driven</h3>
                            <p>Path prefix, public URL, accent colour, and database credentials live in one PHP config file.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ubs-feature">
                            <div class="ubs-icon"><i class="fa-solid fa-pen-nib"></i></div>
                            <h3>Editorial workflow</h3>
                            <p>Authors use the admin UI; visitors see listing and detail pages that match this design system.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="ubs-section">
            <div class="container">
                <h2 class="ubs-section-title">Services (sample)</h2>
                <p class="ubs-section-desc">Placeholder cards for layout testing — swap copy when you wire this into a real marketing site.</p>
                <div class="row g-4 ubs-card-grid">
                    <div class="col-md-4">
                        <div class="ubs-feature">
                            <div class="ubs-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                            <h3>Design systems</h3>
                            <p>Tokens for colour and radius map cleanly to <code class="ubs-code" style="background: #f1f5f9; color: var(--ubs-surface);">--ubs-*</code> variables.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ubs-feature">
                            <div class="ubs-icon"><i class="fa-solid fa-server"></i></div>
                            <h3>Deployment</h3>
                            <p>Copy the folder, set <code class="ubs-code" style="background: #f1f5f9; color: var(--ubs-surface);">path_prefix</code>, import SQL, and publish.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ubs-feature">
                            <div class="ubs-icon"><i class="fa-solid fa-chart-line"></i></div>
                            <h3>SEO-ready</h3>
                            <p>Canonical URLs, meta fields, and optional pretty routes under <code class="ubs-code" style="background: #f1f5f9; color: var(--ubs-surface);">/blog/slug</code>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="ubs-section ubs-section--alt">
            <div class="container">
                <h2 class="ubs-section-title">Testimonials</h2>
                <p class="ubs-section-desc">Static quotes to validate typography and contrast on dark cards.</p>
                <div class="row g-4">
                    <div class="col-md-4">
                        <blockquote class="ubs-quote">
                            <p>We embedded the panel beside our React landing page. The blog never inherited our global button styles — exactly what we needed.</p>
                            <footer>— Priya, Product</footer>
                        </blockquote>
                    </div>
                    <div class="col-md-4">
                        <blockquote class="ubs-quote">
                            <p>Authors see a familiar dashboard; readers get a fast listing and readable articles. Configuration took one afternoon.</p>
                            <footer>— Marcus, Agency lead</footer>
                        </blockquote>
                    </div>
                    <div class="col-md-4">
                        <blockquote class="ubs-quote">
                            <p>Scoped CSS saved us from !important wars with the parent theme. Ship the folder and go.</p>
                            <footer>— Elena, Freelance dev</footer>
                        </blockquote>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="ubs-section">
            <div class="container">
                <div class="row g-5 align-items-start">
                    <div class="col-lg-5">
                        <h2 class="ubs-section-title">Contact</h2>
                        <p class="ubs-section-desc mb-0">Demo form only — wire to mail or CRM when you integrate. Blog detail “Contact” actions can point here with <code class="ubs-code" style="background: #f1f5f9; color: var(--ubs-surface);">index.php#contact</code>.</p>
                    </div>
                    <div class="col-lg-7">
                        <form class="ubs-form row g-3" action="#" method="post" onsubmit="return false;">
                            <div class="col-md-6">
                                <label class="form-label" for="cname">Name</label>
                                <input type="text" class="form-control" id="cname" name="name" placeholder="Your name" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cemail">Email</label>
                                <input type="email" class="form-control" id="cemail" name="email" placeholder="you@example.com" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="cmsg">Message</label>
                                <textarea class="form-control" id="cmsg" name="message" rows="4" placeholder="How can we help?"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn ubs-btn">Send (demo)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
<?php
require __DIR__ . '/includes/portal-close.php';
