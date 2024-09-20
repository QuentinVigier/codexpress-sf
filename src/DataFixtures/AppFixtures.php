<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Like;
use App\Entity\Network;
use App\Entity\Note;
use App\Entity\Offer;
use App\Entity\Subscription;
use App\Entity\View;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slug = null;
    private $hash = null;

    public function __construct(
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $hasher
    ) {
        $this->slug = $slugger;
        $this->hash = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création de catégories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];

        $categoryArray = []; // Ce tableau nous servira pour conserver les objets Category

        foreach ($categories as $title => $icon) {
            $category = new Category(); // Nouvel objet Category
            $category
                ->setTitle($title) // Ajoute le titre
                ->setIcon($icon) // Ajoute l'icone
            ;

            array_push($categoryArray, $category); // Ajout de l'objet
            $manager->persist($category);
        }

         // Création des offres
        // Offre 1 : Freemium
        $offer1 = new Offer();
        $offer1->setName('Freemium')
            ->setPrice('0.00')
            ->setFeatures('Accès limité aux fonctionnalités de base, publicités affichées');
        $manager->persist($offer1);

        // Offre 2 : Premium
        $offer2 = new Offer();
        $offer2->setName('Premium')
            ->setPrice('9.99')
            ->setFeatures('Accès à toutes les fonctionnalités, sans publicité');
        $manager->persist($offer2);

        // Offre 3 : Business
        $offer3 = new Offer();
        $offer3->setName('Business')
            ->setPrice('29.99')
            ->setFeatures('Accès complet, gestion multi-utilisateur, support prioritaire');
        $manager->persist($offer3);

        $manager->flush();

        // 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName; // Génére un username aléatoire
            $usernameFinal = $this->slug->slug($username); // Username en slug
            $user =  new User();
            $user
                ->setEmail($usernameFinal . '@' . $faker->freeEmailDomain)
                ->setUsername($username)
                ->setImage($faker->imageUrl(200, 200, 'people'))
                ->setPassword($this->hash->hashPassword($user, 'admin'))
                ->setRoles(['ROLE_USER'])
            ;
            $manager->persist($user);

            $sub = new Subscription();
            $sub->setOffer($faker->randomElement([$offer1, $offer2, $offer3]))
                ->setCreator($user)
                ->setStartDate(new \DateTimeImmutable())
                ->setEndDate(new \DateTimeImmutable())
            ;
            $manager->persist($sub);

            $network1 = new Network();
            $network1->setName('github')
                ->setUrl('https://github.com/quentin-dev')
                ->setCreator($user)
            ;
            $manager->persist($network1);

            $network2 = new Network();
            $network2->setName('twitter')
                ->setUrl('https://twitter.com/quentin_dev')
                ->setCreator($user)
            ;
            $manager->persist($network2);

            $network3 = new Network();
            $network3->setName('facebook')
                ->setUrl('https://www.facebook.com/in/quentin-dev/')
                ->setCreator($user)
            ;
            $manager->persist($network3);

            for ($j = 0; $j < 10; $j++) {
                $note = new Note();
                $note
                    ->setTitle($faker->sentence())
                    ->setSlug($this->slug->slug($note->getTitle()))
                    ->setContent($faker->randomHtml())
                    ->setPremium($faker->boolean(50))
                    ->setPublic($faker->boolean(50))
                    ->setCreator($user)
                    ->setCategory($faker->randomElement($categoryArray))
                ;
                $manager->persist($note);

                for ($k = 0; $k<500; $k++) {
                    $view = new View();
                    $view
                        ->setNote($note)
                        ->setIpAddress($faker->ipv4)
                    ;
                    $manager->persist($view);
                }

                    $like = new Like();
                    $like
                        ->setNote($note)
                        ->setCreator($user)
                    ;
                    $manager->persist($like);
            }
        }
        $manager->flush();
    }
}
