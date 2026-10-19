@php
    $name = $portfolio->full_name ?: 'Your Name';
    $headline = $portfolio->headline ?: 'Developer / Professional';
    $about = $portfolio->bio ?: 'A snapshot of my work, experience, and the things I love creating.';
    $email = $portfolio->email ?? '';
    $phone = $portfolio->contact_number ?? '';
    $location = $portfolio->address ?? '';
    $photo = $portfolio->profile_image
        ? asset('storage/' . $portfolio->profile_image)
        : null;

    $groupedSections = $portfolio->sections ? $portfolio->sections->groupBy('section_type') : collect();

    // Social links array
    $socials = [];
    if (!empty($portfolio->github)) $socials[] = ['label' => 'GitHub', 'url' => $portfolio->github];
    if (!empty($portfolio->linkedin)) $socials[] = ['label' => 'LinkedIn', 'url' => $portfolio->linkedin];
    if (!empty($portfolio->twitter)) $socials[] = ['label' => 'Twitter / X', 'url' => $portfolio->twitter];
    if (!empty($portfolio->facebook)) $socials[] = ['label' => 'Facebook', 'url' => $portfolio->facebook];

    // Skills array calculation
    $skillsArray = [];
    if ($groupedSections->has('skills')) {
        foreach($groupedSections['skills'] as $item) {
            $sList = is_array($item->content['skills'] ?? null) 
                ? $item->content['skills'] 
                : array_filter(array_map('trim', explode(',', $item->content['skills'] ?? '')));
            $skillsArray = array_merge($skillsArray, $sList);
        }
    } elseif (!empty($portfolio->skills)) {
        $skillsArray = array_filter(array_map('trim', explode(',', $portfolio->skills)));
    }

    $shareUrl = !empty($portfolio->slug) ? url('/p/' . $portfolio->slug) : '';
    $hasId = !empty($portfolio->id);
    $editUrl = $hasId ? route('portfolios.edit', $portfolio->id) : '#';
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name }} — Portfolio</title>
    <link rel="icon" type="image/png" href="{{ asset('images/LOGO.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/templates/simple.css">
</head>
<body class="bg-white text-neutral-900 font-sans antialiased selection:bg-neutral-900 selection:text-white min-h-screen">

    <main class="max-w-2xl mx-auto px-6 py-16 space-y-16">

        <!-- Header / Hero Section -->
        <header class="space-y-6">
            <div class="flex items-center justify-between">
                @if(!empty($photo))
                    <img src="{{ $photo }}" alt="{{ $name }}" class="w-16 h-16 rounded-full object-cover grayscale">
                @else
                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center text-lg font-medium text-neutral-600">
                        {{ strtoupper(substr($name, 0, 2)) }}
                    </div>
                @endif
                <div class="text-right text-xs text-neutral-500 space-y-1">
                    @if($email) <div><a href="mailto:{{ $email }}" class="hover:underline">{{ $email }}</a></div> @endif
                    @if($phone) <div>{{ $phone }}</div> @endif
                    @if($location) <div>{{ $location }}</div> @endif
                </div>
            </div>

            <div class="space-y-2 pt-4">
                <h1 class="text-3xl font-normal tracking-tight text-neutral-900">
                    {{ $name }}
                </h1>
                <p class="text-neutral-500 text-sm">
                    {{ $headline }}
                </p>
            </div>
            
            <p class="text-neutral-600 text-sm leading-relaxed">
                {{ $about }}
            </p>
        </header>

        <hr class="border-neutral-100">

        <!-- PROJECTS SECTION -->
        @if($groupedSections->has('projects') || !empty($portfolio->projects))
            <section id="projects" class="space-y-6">
                <h2 class="text-xs font-mono uppercase tracking-widest text-neutral-400">Projects</h2>
                <div class="space-y-8">
                    @if($groupedSections->has('projects'))
                        @foreach($groupedSections['projects'] as $item)
                            @if(!empty($item->content['title']))
                                <div class="space-y-2">
                                    <div class="flex items-baseline justify-between">
                                        <h3 class="text-sm font-medium text-neutral-900">{{ $item->content['title'] }}</h3>
                                        @if(!empty($item->content['link']))
                                            <a href="{{ $item->content['link'] }}" target="_blank" rel="noopener noreferrer" class="text-xs text-neutral-400 hover:text-neutral-900 transition-colors">
                                                Link →
                                            </a>
                                        @endif
                                    </div>
                                    @if(!empty($item->content['description']))
                                        <p class="text-xs text-neutral-500 leading-relaxed">{{ $item->content['description'] }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </section>
        @endif

        <!-- BACKGROUND (EDUCATION & EXPERIENCE) SECTION -->
        @if($groupedSections->has('education') || !empty($portfolio->experience))
            <section id="experience" class="space-y-6">
                <h2 class="text-xs font-mono uppercase tracking-widest text-neutral-400">Experience & Education</h2>
                <div class="space-y-8">
                    @if(!empty($portfolio->experience))
                        <div class="space-y-1">
                            <h3 class="text-sm font-medium text-neutral-900">Work Experience</h3>
                            <p class="text-xs text-neutral-500 whitespace-pre-line leading-relaxed pt-1">{{ $portfolio->experience }}</p>
                        </div>
                    @endif

                    @if($groupedSections->has('education'))
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-neutral-900">Education</h3>
                            <div class="space-y-4">
                                @foreach($groupedSections['education'] as $item)
                                    @if(!empty($item->content['school']))
                                        <div class="flex justify-between items-baseline text-xs">
                                            <div>
                                                <span class="font-medium text-neutral-900">{{ $item->content['school'] }}</span>
                                                @if(!empty($item->content['degree']))
                                                    <span class="text-neutral-500 block pt-0.5">{{ $item->content['degree'] }}</span>
                                                @endif
                                            </div>
                                            @if(!empty($item->content['year']))
                                                <span class="text-neutral-400 font-mono">{{ $item->content['year'] }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <!-- SKILLS SECTION -->
        @if(count($skillsArray) > 0)
            <section id="skills" class="space-y-4">
                <h2 class="text-xs font-mono uppercase tracking-widest text-neutral-400">Skills</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($skillsArray as $s)
                        <span class="text-xs text-neutral-600 bg-neutral-100 px-2.5 py-1 rounded">
                            {{ $s }}
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- SOCIAL LINKS -->
        @if(count($socials) > 0)
            <section id="social" class="space-y-4">
                <h2 class="text-xs font-mono uppercase tracking-widest text-neutral-400">Connect</h2>
                <div class="flex flex-wrap gap-4 text-xs">
                    @foreach($socials as $l)
                        <a href="{{ $l['url'] }}" target="_blank" rel="noopener noreferrer" class="text-neutral-600 hover:text-neutral-900 underline underline-offset-4">
                            {{ $l['label'] }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Shareable Link Box -->
        @auth
            <section class="border-t border-neutral-100 pt-8 space-y-4">
                <div>
                    <h3 class="text-xs font-mono uppercase tracking-widest text-neutral-400">Publish & Share</h3>
                </div>
                @if($hasId)
                    <div class="flex flex-col sm:flex-row items-center gap-2">
                        <input type="text" id="portfolio-url" readonly value="{{ $shareUrl }}" 
                               class="bg-neutral-50 border border-neutral-200 text-neutral-600 text-xs px-3 py-2 rounded w-full outline-none select-all">
                        <button onclick="copyPortfolioUrl()" id="copy-btn" class="w-full sm:w-auto px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white text-xs rounded transition-all shrink-0">
                            Copy
                        </button>
                    </div>
                    <div class="flex gap-4 pt-2 text-xs">
                        <a href="{{ $editUrl }}" class="text-neutral-600 hover:underline">Edit Portfolio</a>
                        <form action="{{ route('portfolios.destroy', $portfolio->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this portfolio?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                @else
                    <p class="text-xs text-neutral-400">This is a live preview. Choose this template from the setup screen to generate your final portfolio.</p>
                @endif
            </section>
        @endauth

        <script>
            function copyPortfolioUrl() {
                const urlInput = document.getElementById('portfolio-url');
                if(!urlInput) return;
                urlInput.select();
                urlInput.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(urlInput.value);
                const btn = document.getElementById('copy-btn');
                btn.innerText = 'Copied';
                setTimeout(() => {
                    btn.innerText = 'Copy';
                }, 2000);
            }
        </script>

        <!-- Footer -->
        <footer class="pt-8 border-t border-neutral-100 text-xs text-neutral-400 flex justify-between">
            <p>© {{ date('Y') }} {{ $name }}.</p>
            <p>PortGen</p>
        </footer>

    </main>

    <!-- Floating Dashboard Button -->
   <!-- Floating Back / Dashboard Button with Dynamic Preview Support -->
    @auth
        <div style="position: fixed; bottom: 25px; right: 25px; z-index: 100;">
            @if(isset($fromTemplate) && $fromTemplate)
                <a href="{{ route('portfolios.chooseTemplate') }}" class="button" style="box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    &larr; Back to Templates
                </a>
            @else
                <a href="{{ route('home') }}" class="button" style="box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    &larr; Dashboard
                </a>
            @endif
        </div>
    @endauth

</body>
</html>