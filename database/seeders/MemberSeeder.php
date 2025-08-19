<?php

namespace Database\Seeders;

use Botble\Base\Models\BaseModel;
use Botble\Member\Database\Seeders\MemberSeeder as BaseMemberSeeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends BaseMemberSeeder
{
    protected function getMemberData(): array
    {
        $files = $this->uploadFiles('members');

        $now = $this->now();
        $faker = $this->fake();

        // Get parent data
        $parentData = parent::getMemberData();

        // Rebuild parent data with all required fields
        $data = [];
        foreach ($parentData as $member) {
            $data[] = [
                'id' => $member['id'],
                'first_name' => $member['first_name'],
                'last_name' => $member['last_name'],
                'email' => $member['email'],
                'password' => $member['password'],
                'dob' => null,
                'description' => null,
                'phone' => null,
                'confirmed_at' => $member['confirmed_at'],
                'created_at' => $member['created_at'],
                'updated_at' => $member['updated_at'],
            ];
        }

        // Add more realistic members with diverse backgrounds
        $additionalMembers = [
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@techmail.com',
                'dob' => $faker->dateTimeBetween('-35 years', '-25 years')->format('Y-m-d'),
                'description' => 'Senior Software Engineer with 10+ years of experience in cloud architecture and distributed systems.',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@innovate.io',
                'dob' => $faker->dateTimeBetween('-40 years', '-30 years')->format('Y-m-d'),
                'description' => 'Tech entrepreneur and startup advisor. Founded 3 successful SaaS companies.',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily.rodriguez@designhub.com',
                'dob' => $faker->dateTimeBetween('-32 years', '-26 years')->format('Y-m-d'),
                'description' => 'UX/UI Designer specializing in mobile applications and accessibility.',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Kim',
                'email' => 'david.kim@airesearch.org',
                'dob' => $faker->dateTimeBetween('-38 years', '-28 years')->format('Y-m-d'),
                'description' => 'AI Research Scientist focusing on natural language processing and computer vision.',
            ],
            [
                'first_name' => 'Jessica',
                'last_name' => 'Thompson',
                'email' => 'jessica.thompson@securitypro.net',
                'dob' => $faker->dateTimeBetween('-36 years', '-27 years')->format('Y-m-d'),
                'description' => 'Cybersecurity expert and ethical hacker. CISSP certified with expertise in penetration testing.',
            ],
        ];

        $baseId = BaseModel::getTypeOfId() === 'BIGINT' ? 11 : null;

        foreach ($additionalMembers as $index => $member) {
            $createdAt = $faker->dateTimeBetween('-2 years', '-1 week');
            $data[] = [
                'id' => $baseId ? ($baseId + $index) : $faker->uuid(),
                'first_name' => $member['first_name'],
                'last_name' => $member['last_name'],
                'email' => $member['email'],
                'password' => Hash::make('12345678'),
                'dob' => $member['dob'],
                'description' => $member['description'],
                'phone' => $faker->phoneNumber(),
                'confirmed_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $now,
            ];
        }

        // Assign avatars to members
        foreach ($data as $index => &$item) {
            if (! isset($files[$index])) {
                continue;
            }

            $file = $files[$index];

            $item['avatar_id'] = $file['error'] ? 0 : $file['data']->id;
        }

        return $data;
    }
}
