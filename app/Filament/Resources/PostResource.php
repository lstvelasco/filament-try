<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Create New Post')->tabs([
                    Tab::make('Tab 1')->icon('heroicon-o-rectangle-stack')->schema([
                        TextInput::make('title')->rules([
                            'min:2',
                            'max:10',
                        ])->required(),
                        TextInput::make('slug')->unique(ignoreRecord: true)->required(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->label('Category'),
                        ColorPicker::make('color')->required(),
                    ]),
                    Tab::make('Content')->schema([
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ]),
                    Tab::make('Meta')->schema([
                        FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('thumbnails')
                            ->required(),
                        TagsInput::make('tags')->required(),
                        Checkbox::make('published'),
                    ])
                ])->activeTab(3)->persistTabInQueryString(),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('thumbnail')->toggleable(),
                ColorColumn::make('color')->toggleable(),
                TextColumn::make('title')->sortable()->searchable()->toggleable(),
                TextColumn::make('slug')->sortable()->searchable()->toggleable(),
                TagsColumn::make('tags')->toggleable(),
                TextColumn::make('category.name')->sortable()->searchable()->toggleable(),
                CheckboxColumn::make('published')->toggleable(),
                TextColumn::make('created_at')->label('Created on')->dateTime('F j,Y h:m A')->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class
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
