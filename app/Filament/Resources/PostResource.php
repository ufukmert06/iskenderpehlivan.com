<?php

namespace App\Filament\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Yazi';

    protected static ?string $pluralModelLabel = 'Yazilar';

    protected static ?string $navigationLabel = 'Yazilar';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tip')
                    ->options([
                        'blog' => 'Blog Yazisi',
                        'page' => 'Sayfa',
                    ])
                    ->required()
                    ->default('blog')
                    ->native(false),

                Forms\Components\TextInput::make('slug_base')
                    ->label('Temel Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Dil bagimsiz temel slug (ilk ceviriden otomatik uretilir)')
                    ->dehydrated(),

                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options([
                        'draft' => 'Taslak',
                        'published' => 'Yayinlandi',
                        'archived' => 'Arsivlendi',
                    ])
                    ->required()
                    ->default('draft')
                    ->native(false),

                Forms\Components\FileUpload::make('featured_image')
                    ->label('One Cikan Gorsel')
                    ->image()
                    ->directory('posts/featured')
                    ->disk('public')
                    ->maxSize(5120)
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ]),

                Forms\Components\Select::make('user_id')
                    ->label('Yazar')
                    ->relationship('user', 'name')
                    ->required()
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Siralama')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Select::make('categories')
                    ->label('Kategoriler')
                    ->relationship('categories', 'slug_base')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\Select::make('type')
                            ->label('Tip')
                            ->options([
                                'blog' => 'Blog',
                                'page' => 'Sayfa',
                            ])
                            ->required()
                            ->default('blog'),
                        Forms\Components\TextInput::make('slug_base')
                            ->label('Temel Slug')
                            ->required()
                            ->unique(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Siralama')
                            ->numeric()
                            ->default(0),
                    ]),

                Forms\Components\Select::make('tags')
                    ->label('Etiketler')
                    ->relationship('tags', 'slug_base')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\Select::make('type')
                            ->label('Tip')
                            ->options([
                                'post' => 'Yazı',
                                'category' => 'Kategori',
                            ])
                            ->required()
                            ->default('post'),
                        Forms\Components\TextInput::make('slug_base')
                            ->label('Temel Slug')
                            ->required()
                            ->unique(),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Renk'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Siralama')
                            ->numeric()
                            ->default(0),
                    ]),

                Forms\Components\Section::make('Icerik Cevirileri')
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->label('Dil')
                                    ->options([
                                        'tr' => 'Turkce',
                                        'en' => 'English',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('title')
                                    ->label('Baslik')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old ?? '')) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));

                                        // Ilk ceviride slug_base'i de guncelle
                                        if ($get('../../slug_base') === Str::slug($old ?? '') || empty($get('../../slug_base'))) {
                                            $set('../../slug_base', Str::slug($state));
                                        }
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Dile ozel URL slug (otomatik uretilir, manuel duzenlenebilir)'),

                                Forms\Components\Textarea::make('excerpt')
                                    ->label('Ozet')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                TinyEditor::make('content')
                                    ->label('Icerik')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('posts/attachments')
                                    ->profile('full')
                                    ->columnSpanFull(),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Yayinlanma Tarihi')
                                    ->displayFormat('d/m/Y H:i')
                                    ->timezone('Europe/Istanbul'),

                                Forms\Components\Section::make('SEO Ayarlari')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Meta Baslik')
                                            ->maxLength(255)
                                            ->helperText('SEO icin baslik (bos birakilirsa baslik kullanilir)'),

                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Meta Aciklama')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('SEO icin aciklama (maksimum 160 karakter)'),

                                        Forms\Components\Textarea::make('meta_keywords')
                                            ->label('Meta Anahtar Kelimeler')
                                            ->rows(2)
                                            ->helperText('Virgulle ayrilmis anahtar kelimeler'),

                                        Forms\Components\TextInput::make('canonical_url')
                                            ->label('Canonical URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->helperText('Duplicate content icin canonical URL'),

                                        Forms\Components\Select::make('robots')
                                            ->label('Robots Meta Tag')
                                            ->options([
                                                'index, follow' => 'Index, Follow',
                                                'noindex, follow' => 'Noindex, Follow',
                                                'index, nofollow' => 'Index, Nofollow',
                                                'noindex, nofollow' => 'Noindex, Nofollow',
                                            ])
                                            ->native(false),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Forms\Components\Section::make('Open Graph Ayarlari')
                                    ->schema([
                                        Forms\Components\FileUpload::make('og_image')
                                            ->label('OG Gorsel')
                                            ->image()
                                            ->directory('posts/og')
                                            ->disk('public')
                                            ->maxSize(5120)
                                            ->imageEditor(),

                                        Forms\Components\TextInput::make('og_title')
                                            ->label('OG Baslik')
                                            ->maxLength(255)
                                            ->helperText('Open Graph icin baslik (bos birakilirsa baslik kullanilir)'),

                                        Forms\Components\Textarea::make('og_description')
                                            ->label('OG Aciklama')
                                            ->rows(3)
                                            ->maxLength(200)
                                            ->helperText('Open Graph icin aciklama'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['locale'] ?? null)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->reorderable(false)
                            ->addActionLabel('Yeni Ceviri Ekle'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'blog' => 'success',
                        'page' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('slug_base')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Yazar')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Siralama')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('tags_count')
                    ->label('Etiket Sayısı')
                    ->counts('tags')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Olusturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Guncelleme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tip')
                    ->options([
                        'blog' => 'Blog Yazisi',
                        'page' => 'Sayfa',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'draft' => 'Taslak',
                        'published' => 'Yayinlandi',
                        'archived' => 'Arsivlendi',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Duzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
