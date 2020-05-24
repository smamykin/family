<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An offer to transfer some rights to an item or to provide a service — for example, an offer to sell tickets to an event, to rent the DVD of a movie, to stream a TV show over the internet, to repair a motorcycle, or to loan a book.\\n\\nFor \[GTIN\](http://www.gs1.org/barcodes/technical/idkeys/gtin)-related fields, see \[Check Digit calculator\](http://www.gs1.org/barcodes/support/check\_digit\_calculator) and \[validation guide\](http://www.gs1us.org/resources/standards/gtin-validation-guide) from \[GS1\](http://www.gs1.org/).
 *
 * @see http://schema.org/Offer Documentation on Schema.org
 *
 * @ORM\Entity
 * @ApiResource(
 *iri="http://schema.org/Offer",
 *normalizationContext={"groups"={"read"}},denormalizationContext={"groups"={"write"}}, attributes={"pagination_enabled"=false},
 * itemOperations={
 *     "get",
 *     "delete"={
 *         "security"="object.getUser() == user",
 *          "security_message"="Sorry, but you are not the offer author."
 *     },
 *     "patch"={
 *          "validation_groups"={"patchValidation"}
 *     }
 * },
 * collectionOperations={"get", "post"={"validation_groups"={"postValidation"}}}
 *)
 */
class Offer
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @var string URL of the item
     *
     * @ORM\Column(type="text")
     * @ApiProperty(iri="http://schema.org/url")
     * @Assert\Url
     * @Assert\NotNull
     * @Groups({"read", "write"})
     */
    private $url;

    /**
     * @var float The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.\\n\\nUsage guidelines:\\n\\n\* Use the \[\[priceCurrency\]\] property (with \[ISO 4217 codes\](http://en.wikipedia.org/wiki/ISO\_4217#Active\_codes) e.g. "USD") instead of including \[ambiguous symbols\](http://en.wikipedia.org/wiki/Dollar\_sign#Currencies\_that\_use\_the\_dollar\_or\_peso\_sign) such as '$' in the value.\\n\* Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.\\n\* Note that both \[RDFa\](http://www.w3.org/TR/xhtml-rdfa-primer/#using-the-content-attribute) and Microdata syntax allow the use of a "content=" attribute for publishing simple machine-readable values alongside more human-friendly formatting.\\n\* Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.
     *
     * @ORM\Column(type="float")
     * @ApiProperty(iri="http://schema.org/price")
     * @Assert\NotNull
     * @Groups({"read", "write"})
     */
    private $price;

    /**
     * @var string the currency (in 3-letter ISO 4217 format) of the price or a price component, when attached to \[\[PriceSpecification\]\] and its subtypes
     *
     * @ORM\Column(type="text")
     * @ApiProperty(iri="http://schema.org/priceCurrency")
     * @Assert\NotNull(groups={"patchValidation"})
     * @Groups({"read", "write"})
     * @Assert\NotBlank(groups={"postValidation"})
     */
    private $priceCurrency;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="offers")
     * @ApiProperty(attributes={"fetchEager": false})
     * @Groups({"write"})
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    public function __toString()
    {
        return $this->getUrl();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param float $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function setPriceCurrency(string $priceCurrency): void
    {
        $this->priceCurrency = $priceCurrency;
    }

    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

